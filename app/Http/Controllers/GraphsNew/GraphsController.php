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
            'labelHeading' => Lang::get('graphs-new.groups.heading'),
            'labelHeader' => Lang::get('graphs-new.groups.header'),
            'chooseYear' => true,
            'chooseGroup' => true,
            'chooseChartStyle' => true,
            'chooseChartType' => true,
            'stackedGroups' => [],
        ]);
    }

    public function indexIncomeVsExpense()
    {
        return $this->index([
            'dataSource' => 'income-vs-expense',
            'labelHeading' => Lang::get('graphs-new.income-vs-expense.heading'),
            'labelHeader' => Lang::get('graphs-new.income-vs-expense.header'),
            'chooseYear' => true,
            'chooseGroup' => false,
            'chooseChartStyle' => true,
            'chooseChartType' => true,
            'stackedGroups' => [
                'income' => ['income'],
                'expense-correction' => ['expense', 'correction'],
            ],
        ]);
    }

    public function indexExpenses()
    {
        return $this->index([
            'dataSource' => 'expenses',
            'labelHeading' => Lang::get('graphs-new.expenses.heading'),
            'labelHeader' => Lang::get('graphs-new.expenses.header'),
            'chooseYear' => true,
            'chooseGroup' => false,
            'chooseChartStyle' => true,
            'chooseChartType' => true,
            'stackedGroups' => [],
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
        $stackedGroups = $params['stackedGroups'] ?? [];

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
        $months = Lang::get('graphs-new.months');
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

        // redirect have to be done acording to the data source
        if ($needsRedirect) {
            if ($dataSource === 'income-vs-expense') {
                return redirect()->route('graphs-new.income-vs-expense', $query);
            } elseif ($dataSource === 'expenses') {
                return redirect()->route('graphs-new.expenses', $query);
            } else {
                return redirect()->route('graphs-new.groups', $query);
            }
        }

        // Get the collection_id for this user (assuming one collection per user)
        $collectionId = $user->collection_id;
        $groupNames = [];
        $selectedGroup = null;
        $graphLabels = [];
        $graphData = [];

        if ($dataSource == 'groups') {

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
                    if ($collectionId == 1) {
                        $subgroupNames[$subgroup_id] = Lang::get('std-groups.subgroup-name.' .$subgroupNamesFromDb[$subgroup_id]) ?? 'Unknown';
                    } else {
                        $subgroupNames[$subgroup_id] = $subgroupNamesFromDb[$subgroup_id] ?? 'Unknown';
                    }
                }
            }

            $graphLabels = $subgroupNames;
            $graphData = $subgroupData;
        }
        
        if ($dataSource == 'income-vs-expense') {
            // Logic to prepare data for income vs expense graph
            $incomeData = array_fill(0, 12, 0);
            $expenseData = array_fill(0, 12, 0);
            $correctionData = array_fill(0, 12, 0);

            if ($selectedYear) {
                // Get all headers for the selected year and user
                $headers = Header::where('user_id', $user->id)
                    ->whereYear('date', $selectedYear)
                    ->get();

                // For each header, sum income, expense, and correction by month
                foreach ($headers as $header) {
                    $monthIdx = (int)date('n', strtotime($header->date)) - 1;
                    // For each item in header, check group type
                    foreach ($header->items as $item) {
                        $group = $item->group;
                        if ($group && isset($group->type)) {
                            if ($group->type == 1) { // income
                                $incomeData[$monthIdx] += (float)$item->amount;
                            } elseif ($group->type == 2) { // expense
                                $expenseData[$monthIdx] += (float)$item->amount;
                            } elseif ($group->type == 3) { // correction
                                $correctionData[$monthIdx] += (float)$item->amount;
                            }
                        }
                    }
                }
            }

            $graphLabels = [
                'income' => Lang::get('graphs-new.income-vs-expense.income'),
                'expense' => Lang::get('graphs-new.income-vs-expense.expense'),
                'correction' => Lang::get('graphs-new.income-vs-expense.correction'),
            ];
            $graphData = [
                'income' => $incomeData,
                'expense' => $expenseData,
                'correction' => $correctionData,
            ];
        }

        if ($dataSource == 'expenses') {
            // Logic to prepare data for expenses graph: columns for every group of type 2 (expense) plus correction
            $expenseGroups = Group::where('collection_id', $collectionId)->where('type', 2)->orderBy('name')->get();
            $groupIds = $expenseGroups->pluck('id')->toArray();
            // Use translated names if collectionId == 1, else use DB names
            if ($collectionId == 1) {
                $groupNamesMap = $expenseGroups->mapWithKeys(function($group) {
                    return [$group->id => Lang::get('std-groups.group-name.' . $group->name)];
                })->toArray();
            } else {
                $groupNamesMap = $expenseGroups->pluck('name', 'id')->toArray();
            }

            // Initialize data arrays for each group and for correction
            $expenseData = [];
            foreach ($groupIds as $gid) {
                $expenseData[$gid] = array_fill(0, 12, 0);
            }
            $correctionData = array_fill(0, 12, 0);

            if ($selectedYear) {
                $headers = Header::where('user_id', $user->id)
                    ->whereYear('date', $selectedYear)
                    ->get();
                foreach ($headers as $header) {
                    $monthIdx = (int)date('n', strtotime($header->date)) - 1;
                    foreach ($header->items as $item) {
                        $group = $item->group;
                        if ($group && isset($group->type)) {
                            if ($group->type == 2 && isset($expenseData[$item->group_id])) {
                                $expenseData[$item->group_id][$monthIdx] += (float)$item->amount;
                            } elseif ($group->type == 3) { // correction
                                $correctionData[$monthIdx] += (float)$item->amount;
                            }
                        }
                    }
                }
            }

            // Prepare labels and data for chart
            $graphLabels = $groupNamesMap;
            $graphLabels['correction'] = Lang::get('graphs-new.expenses.correction');
            $graphData = $expenseData;
            $graphData['correction'] = $correctionData;
        }

        $labelGrouped = Lang::get('graphs-new.grouped');
        $labelStacked = Lang::get('graphs-new.stacked');
        $labelColumns = Lang::get('graphs-new.columns');
        $labelLines = Lang::get('graphs-new.lines');
        $labelYear = Lang::get('graphs-new.year');
        $labelGroup = Lang::get('graphs-new.group');
        $labelChartStyle = Lang::get('graphs-new.chart-style');
        $labelChartType = Lang::get('graphs-new.chart-type');

        // Default method to handle the index view
        return view('graphs-new.graphs', compact(
            'chooseYear',
            'chooseGroup',
            'chooseChartStyle',
            'chooseChartType',
            'years',
            'selectedYear',
            'months',
            'currentChartStyle',
            'currentChartType',
            'labelGrouped',
            'labelStacked',
            'labelColumns',
            'labelLines',
            'labelHeading',
            'labelHeader',
            'labelYear',
            'labelGroup',
            'labelChartStyle',
            'labelChartType',
            'groupNames',
            'graphLabels',
            'graphData',
            'selectedGroup',
            'stackedGroups',
        ));
    }
}
