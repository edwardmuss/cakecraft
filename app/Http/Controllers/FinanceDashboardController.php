<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        $filterDate = $request->month ? Carbon::parse($request->month) : Carbon::now();
        $isMonthFilter = $request->filled('month');

        // NEW: Formatted display string for the UI
        $displayPeriod = $isMonthFilter
            ? $filterDate->format('F Y')
            : 'Year to Date (Jan 1 - Dec 31, ' . $filterDate->format('Y') . ')';

        $startDate = $isMonthFilter ? $filterDate->copy()->startOfMonth() : $filterDate->copy()->startOfYear();
        $endDate = $isMonthFilter ? $filterDate->copy()->endOfMonth() : $filterDate->copy()->endOfYear();

        // 1. KPI WIDGETS
        $expectedIncome = Invoice::where('branch_id', $branchId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $actualIncome = Payment::where('branch_id', $branchId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $actualExpense = Expense::where('branch_id', $branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('total_amount');

        $netProfit = $actualIncome - $actualExpense;

        // 2. CHART DATA: LAST 12 MONTHS
        $chartMonths = [];
        $chartExpected = [];
        $chartActual = [];
        $chartExpense = [];

        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $chartMonths[] = $monthStart->format('M Y');

            $chartExpected[] = (float) Invoice::where('branch_id', $branchId)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $chartActual[] = (float) Payment::where('branch_id', $branchId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $chartExpense[] = (float) Expense::where('branch_id', $branchId)
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
        }

        // 3. EXPENSE BREAKDOWN BY CATEGORY (All data for table)
        $allExpenseCategories = Expense::where('expenses.branch_id', $branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', DB::raw('SUM(expenses.total_amount) as total'))
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get();

        // Take Top 5 and cast to FLOAT so the Donut Chart doesn't break
        $top5Categories = $allExpenseCategories->take(5);
        $pieLabels = $top5Categories->pluck('name')->toArray();
        $pieData = $top5Categories->pluck('total')->map(function ($val) {
            return (float) $val;
        })->toArray();

        // 4. EXPENSE BREAKDOWN BY SUPPLIER (All data for table)
        $supplierTableData = Expense::where('expenses.branch_id', $branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->whereNotNull('expenses.supplier_id')
            ->join('suppliers', 'expenses.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.name', DB::raw('SUM(expenses.total_amount) as total'))
            ->groupBy('suppliers.name')
            ->orderByDesc('total')
            ->get();

        return view('finance.dashboard', compact(
            'expectedIncome',
            'actualIncome',
            'actualExpense',
            'netProfit',
            'chartMonths',
            'chartExpected',
            'chartActual',
            'chartExpense',
            'pieLabels',
            'pieData',
            'isMonthFilter',
            'displayPeriod',
            'allExpenseCategories',
            'supplierTableData'
        ));
    }
}
