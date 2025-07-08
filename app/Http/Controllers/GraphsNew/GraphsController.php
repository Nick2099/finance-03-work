<?php

namespace App\Http\Controllers\GraphsNew;

use App\Models\Group;
use App\Models\Header;
use App\Models\Subgroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class GraphsController extends Controller
{
    public function indexGroups()
    {
        return $this->index([
            'dataSource' => 'groups',
            'labelHeading' => Lang::get('charts-new.groups.heading'),
            'labelHeader' => Lang::get('charts-new.groups.header'),
            'chooseYear' => true,
            'chooseGroup' => true,
            'chooseChartStyle' => true,
            'chooseChartType' => true,
        ]);
    }

    public function indexIncomeVsExpense()
    {
        return $this->index([
            'dataSource' => 'income-vs-expense',
            'labelHeading' => Lang::get('charts-new.income-vs-expense.heading'),
            'labelHeader' => Lang::get('charts-new.income-vs-expense.header'),
            'chooseYear' => true,
            'chooseGroup' => false,
            'chooseChartStyle' => true,
            'chooseChartType' => true,
        ]);
    }

    public function indexCashFlow()
    {
        // Logic to fetch and prepare data for the cash flow graph
    }

    public function index(array $params = [])
    {
        $user = Auth::user();
        if (!$user) {
            // Optionally, redirect to login or show an error view
            return redirect()->route('login');
        }

        $dataSource = $params['dataSource'] ?? null;
        $labelHeading = $params['labelHeading'] ?? 'Graphs';
        $labelHeader = $params['labelHeader'] ?? 'Graphs Overview';
        $chooseYear = $params['chooseYear'] ?? false;
        $chooseGroup = $params['chooseGroup'] ?? false;
        $chooseChartStyle = $params['chooseChartStyle'] ?? false;
        $chooseChartType = $params['chooseChartType'] ?? false;

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
        $months = Lang::get('charts-new.months');
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

        $query = request()->query();
        $needsRedirect = false;

        // Always use validated values in the URL
        if (($query['chartStyle'] ?? 'bar') !== $currentChartStyle) {
            $query['chartStyle'] = $currentChartStyle;
            $needsRedirect = true;
        }
        if (($query['chartType'] ?? 'grouped') !== $currentChartType) {
            $query['chartType'] = $currentChartType;
            $needsRedirect = true;
        }

        if ($needsRedirect) {
            return redirect()->route('graphs.exgr', $query);
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
        
        $labelYear = Lang::get('charts-new.year');
        $labelGroup = Lang::get('charts-new.group');
        $labelChartStyle = Lang::get('charts-new.chart-style');
        $labelChartType = Lang::get('charts-new.chart-type');

        // Default method to handle the index view
        return view('graphs-new.graphs', compact(
            'dataSource',
            'chooseYear',
            'chooseGroup',
            'chooseChartStyle',
            'chooseChartType',
            'years',
            'selectedYear',
            'months',
            'currentChartStyle',
            'currentChartType',
            'labelHeading',
            'labelHeader',
            'labelYear',
            'labelGroup',
            'labelChartStyle',
            'labelChartType',
            'groupNames',
            'subgroupData',
            'subgroupNames',
            'selectedGroup'
        ));
    }
}
