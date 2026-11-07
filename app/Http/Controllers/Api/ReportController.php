<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $branchId = $request->user()->branch_id;
        $selectedMonth = $request->get('communion_month', now()->format('Y-m'));

        // Start Query
        $query = Invoice::with(['customer', 'items'])
            ->where('branch_id', $branchId)
            ->where('status', '!=', 'cancelled');

        // --- Apply Month Filter ---
        if ($request->filled('communion_month')) {
            $query->where('communion_month', $request->communion_month);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('export_excel') && $request->export_excel == '1') {
            return $this->generateExcel($query->get(), $selectedMonth);
        }

        // --- Adaptive Ordering ---
        $invoices = $query->orderBy('communion_month', 'desc')
            ->orderBy('communion_week', 'desc')
            ->orderByRaw('COALESCE(communion_date, due_date) DESC')
            ->get();

        $customers = Customer::where('branch_id', $branchId)->orderBy('organization_name', 'asc')->get();

        $totalRevenue = $invoices->sum('total_amount');
        $outstandingBalance = $totalRevenue - $invoices->sum('amount_paid');

        $totalDeliveredTrays = 0;
        foreach ($invoices as $inv) {
            $totalDeliveredTrays += $inv->items
                ->whereIn('tray_type', ['recyclable', 'rec'])
                ->sum('tray_count');
        }

        $totalReturnedTrays = $invoices->sum('returned_trays');
        $missingTrays = $totalDeliveredTrays - $totalReturnedTrays;

        return response()->json([
            'aggregates' => [
                'total_revenue' => $totalRevenue,
                'outstanding_balance' => $outstandingBalance,
                'total_delivered_trays' => $totalDeliveredTrays,
                'total_returned_trays' => $totalReturnedTrays,
                'missing_trays' => $missingTrays,
            ],
            'customers' => $customers,
            'invoices' => $invoices
        ]);
    }

    private function generateExcel($invoices, $month)
    {
        $timestamp = now()->format('Y_m_d_His');
        // This streams the multi-sheet Excel file directly to Flutter!
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlySalesExport($invoices, $month), "Sales_Report_{$month}_{$timestamp}.xlsx");
    }

    public function indexOld(Request $request)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $branchId = $request->user()->branch_id;

        // Start Query
        $query = Invoice::with(['customer', 'items'])->where('branch_id', $branchId)->where('status', '!=', 'cancelled');

        // Apply Filters
        if ($request->filled('start_date')) $query->whereDate('communion_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('communion_date', '<=', $request->end_date);
        if ($request->filled('customer_id')) $query->where('customer_id', $request->customer_id);

        // ==========================================
        // EXCEL EXPORT INTERCEPTOR
        // ==========================================
        if ($request->has('export_excel') && $request->export_excel == '1') {
            return $this->generateExcel($query->get());
        }

        // Standard API Data Load
        $invoices = $query->orderBy('communion_date', 'desc')->get();
        $customers = Customer::where('branch_id', $branchId)->orderBy('organization_name', 'asc')->get();

        $totalRevenue = $invoices->sum('total_amount');
        $outstandingBalance = $totalRevenue - $invoices->sum('amount_paid');

        $totalDeliveredCups = 0;
        foreach ($invoices as $inv) {
            $totalDeliveredCups += $inv->items->sum('quantity');
        }

        $totalReturnedCups = $invoices->sum('returned_trays');
        $missingCups = $totalDeliveredCups - $totalReturnedCups;

        return response()->json([
            'aggregates' => [
                'total_revenue' => $totalRevenue,
                'outstanding_balance' => $outstandingBalance,
                'total_delivered_cups' => $totalDeliveredCups,
                'total_returned_trays' => $totalReturnedCups,
                'missing_cups' => $missingCups,
            ],
            'customers' => $customers,
            'invoices' => $invoices
        ]);
    }

    private function generateExcel2($invoices)
    {
        $productNames = [];
        foreach ($invoices as $inv) {
            foreach ($inv->items as $item) {
                if (!in_array($item->item_name, $productNames)) {
                    $productNames[] = $item->item_name;
                }
            }
        }
        sort($productNames);

        $weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $cupsData = [];
        $salesData = [];

        foreach ($weeks as $week) {
            $salesData[$week] = 0;
            foreach ($productNames as $pName) {
                $cupsData[$week][$pName] = 0;
            }
        }

        foreach ($invoices as $inv) {
            // --- Fallback Logic ---
            $targetDate = $inv->communion_date ?? $inv->due_date;

            // Skip only if BOTH dates are entirely missing
            if (!$targetDate) continue;

            // Parse the fallback date instead of the hardcoded communion_date
            $date = Carbon::parse($targetDate);

            $weekNum = ceil($date->day / 7);
            $weekKey = 'Week ' . ($weekNum > 5 ? 5 : $weekNum);

            $salesData[$weekKey] += $inv->total_amount;
            foreach ($inv->items as $item) {
                $cupsData[$weekKey][$item->item_name] += $item->quantity;
            }
        }

        $timestamp = now()->format('Y_m_d_His');
        // This will stream the Excel file directly to the Mobile app!
        return Excel::download(new \App\Exports\WeeklySalesExport($productNames, $weeks, $cupsData, $salesData), "Sales_Report_{$timestamp}.xlsx");
    }
}
