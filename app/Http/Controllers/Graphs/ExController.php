<?php

namespace App\Http\Controllers\Graphs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Header;
use Illuminate\Support\Facades\Lang;

class ExController extends Controller
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

        $groupNames = []; // e.g. ['Food', 'Transport', 'Utilities']
        $groupData = [];  // e.g. [ [100, 120, ...], [80, 90, ...], [50, 60, ...] ]

        if ($selectedYear) {
            // Get all headers for the selected year and user
            $headers = Header::where('user_id', $user->id)
                ->whereYear('date', $selectedYear)
                ->get();

            // For each header, sum only expense amounts by group and month
            foreach ($headers as $header) {
                $monthIdx = (int)date('n', strtotime($header->date)) - 1;
                // For each item in header, check group_id and group type
                foreach ($header->items as $item) {
                    $group = $item->group;
                    if (!$group || (isset($group->type) && $group->type !== 2)) {
                        continue; // Only sum if group type is 'expense'
                    }
                    $group_id = $item->group_id;
                    // Initialize array for this group if not set
                    if (!isset($groupData[$group_id])) {
                        $groupData[$group_id] = array_fill(0, 12, 0);
                    }
                    $groupData[$group_id][$monthIdx] += $item->amount;
                }
            }

            // Only fetch names for group_ids present in $groupData
            $groupIds = array_keys($groupData);
            $groupNamesFromDb = \App\Models\Group::whereIn('id', $groupIds)->pluck('name', 'id');
            foreach (array_keys($groupData) as $group_id) {
                $groupNames[$group_id] = $groupNamesFromDb[$group_id] ?? 'Unknown';
            }
        }

        $year = __('charts.year');
        $heading = __('charts.ex.heading');
        return view('graphs.ex', compact('months', 'years', 'selectedYear', 'heading', 'year', 'groupData', 'groupNames'));
    }
}
