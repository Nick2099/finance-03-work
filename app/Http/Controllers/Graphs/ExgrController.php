<?php

namespace App\Http\Controllers\Graphs;

use App\Models\Group;
use App\Models\Header;
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


        // Get the collection_id for this user (assuming one collection per user)
        $collectionId = $user->collection_id;
        // Get only groups that belong to this collection
        $userGroups = Group::where('collection_id', $collectionId)->orderBy('name')->get();
        $groupNames = $userGroups;

        // Determine selected group (from GET or default to first)
        $selectedGroup = request('group');
        if (!$selectedGroup || !$userGroups->pluck('id')->contains($selectedGroup)) {
            $selectedGroup = $userGroups->first() ? $userGroups->first()->id : null;
        }

        $groupData = [];
        if ($selectedYear && $selectedGroup) {
            // Get all headers for the selected year and user
            $headers = Header::where('user_id', $user->id)
                ->whereYear('date', $selectedYear)
                ->get();

            // For each header, sum only expense amounts for the selected group by month
            foreach ($headers as $header) {
                $monthIdx = (int)date('n', strtotime($header->date)) - 1;
                foreach ($header->items as $item) {
                    $group = $item->group;
                    if (!$group || (isset($group->type) && $group->type !== 2)) {
                        continue; // Only sum if group type is 'expense'
                    }
                    if ($item->group_id != $selectedGroup) {
                        continue; // Only include items for the selected group
                    }
                    if (!isset($groupData[$selectedGroup])) {
                        $groupData[$selectedGroup] = array_fill(0, 12, 0);
                    }
                    $groupData[$selectedGroup][$monthIdx] += $item->amount;
                }
            }
        }

        $year = __('charts.year');
        $heading = __('charts.exgr.heading');
        return view('graphs.exgr', compact('months', 'years', 'selectedYear', 'heading', 'year', 'groupData', 'groupNames', 'selectedGroup'));
    }
}
