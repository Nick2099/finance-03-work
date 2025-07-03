<?php

namespace App\Http\Controllers\Graphs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Header;
use App\Models\Item;
use App\Models\Group;
use Illuminate\Support\Facades\Lang;

class InExController extends Controller
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

        $income = __('charts.income');
        $expense = __('charts.expense');
        $correction = __('charts.correction');
        return view('graphs.inex', compact('months', 'incomeData', 'expenseData', 'correctionData', 'years', 'selectedYear', 'income', 'expense', 'correction'));
    }
}
