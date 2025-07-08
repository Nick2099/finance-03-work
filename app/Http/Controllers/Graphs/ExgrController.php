<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Group;
use App\Models\Header;
use App\Models\Subgroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ExgrController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            // Optionally, redirect to login or show an error view
            return redirect()->route('login');
        }
        // Get all years from headers for this user
        $years = Header::where('user_id', $user->id)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Determine selected year (from GET or default to latest)
        $selectedYear = request('year');
        if (!$selectedYear || !in_array($selectedYear, $years)) {
            $selectedYear = $years ? max($years) : null;
        }

        // Prepare months labels
        $months = Lang::get('charts.months');
        if (!is_array($months) || count($months) !== 12) {
            $months = config('appoptions.months');
        }

        $currentChartStyle = request('chartStyle');
        if (!$currentChartStyle || !in_array($currentChartStyle, ['bar', 'line'])) {
            $currentChartStyle = 'bar'; // default
        }

        $currentChartType = request('chartType');
        if (!$currentChartType || !in_array($currentChartType, ['grouped', 'stacked'])) {
            $currentChartType = 'grouped'; // default
        }
        if ($currentChartStyle === 'line') {
            $currentChartType = 'grouped'; // Reset to grouped for line charts
        }

        // Get the collection_id for this user (assuming one collection per user)
        $collectionId = $user->collection_id;
        // Get only groups that belong to this collection
        $groupNames = Group::where('collection_id', $collectionId)->orderBy('name')->get();

        // Determine selected group (from GET or default to first)
        $selectedGroup = request('group');
        if (!$selectedGroup || !$groupNames->pluck('id')->contains($selectedGroup)) {
            $selectedGroup = $groupNames->first() ? $groupNames->first()->id : null;
        }

        $subgroupNames = [];
        $subgroupData = [];
        if ($selectedYear && $selectedGroup) {
            // Get all headers for the selected year and user
            $headers = Header::where('user_id', $user->id)
                ->whereYear('date', $selectedYear)
                ->get();

            foreach ($headers as $header) {
                $monthIdx = (int)date('n', strtotime($header->date)) - 1;
                foreach ($header->items as $item) {
                    $group = $item->group;
                    if (!$group) {
                        continue;
                    }
                    if ($item->group_id != $selectedGroup) {
                        continue; // Only include items for the selected group
                    }
                    if (!isset($subgroupData[$item->subgroup_id])) {
                        $subgroupData[$item->subgroup_id] = array_fill(0, 12, 0);
                    }
                    $subgroupData[$item->subgroup_id][$monthIdx] += $item->amount;
                }
            }

            // Only fetch names for subgroup_ids present in $subgroupData
            $subgroupIds = array_keys($subgroupData);
            $subgroupNamesFromDb = Subgroup::whereIn('id', $subgroupIds)->pluck('name', 'id');
            foreach (array_keys($subgroupData) as $subgroup_id) {
                $subgroupNames[$subgroup_id] = $subgroupNamesFromDb[$subgroup_id] ?? 'Unknown';
            }
        }

        $year = __('charts.year');
        $heading = __('charts.exgr.heading');
        return view('graphs.exgr', compact('months', 'years', 'selectedYear', 'heading', 'year', 'subgroupData', 'groupNames', 'subgroupNames', 'selectedGroup', 'currentChartType', 'currentChartStyle'));
    }
}
