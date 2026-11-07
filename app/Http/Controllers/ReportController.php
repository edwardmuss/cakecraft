<?php

namespace App\Http\Controllers;

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
        abort_if(!Auth::user()->can('view financials'), 403, 'Unauthorized action.');

        $branchId = Auth::user()->branch_id;
        $customers = Customer::where('branch_id', $branchId)->orderBy('organization_name', 'asc')->get();

        // Default to current month if no filter is applied
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

        // ==========================================
        // EXCEL EXPORT INTERCEPTOR
        // ==========================================
        if ($request->has('export_excel') && $request->export_excel == '1') {
            return $this->generateExcel($query->get(), $selectedMonth);
        }

        // --- Adaptive Ordering (Prioritize new Month/Week, fallback to legacy dates) ---
        $invoices = $query->orderBy('communion_month', 'desc')
            ->orderBy('communion_week', 'desc')
            ->orderByRaw('COALESCE(communion_date, due_date) DESC')
            ->get();

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

        // return $invoices;

        return view('reports.index', compact('invoices', 'customers', 'request', 'totalRevenue', 'outstandingBalance', 'totalDeliveredTrays', 'totalReturnedTrays', 'missingTrays'));
    }

    private function generateExcel($invoices, $month)
    {
        $timestamp = now()->format('Y_m_d_His');
        // Pass the grouped invoices to the Exporter
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlySalesExport($invoices, $month), "Sales_Report_{$month}_{$timestamp}.xlsx");
    }

    public function indexOld(Request $request)
    {
        abort_if(!Auth::user()->can('view financials'), 403, 'Unauthorized action.');

        $branchId = Auth::user()->branch_id;
        $customers = Customer::where('branch_id', $branchId)->orderBy('organization_name', 'asc')->get();

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

        // Standard Page Load
        $invoices = $query->orderBy('communion_date', 'desc')->get();

        $totalRevenue = $invoices->sum('total_amount');

        $totalDeliveredCups = 0;
        foreach ($invoices as $inv) {
            $totalDeliveredCups += $inv->items->sum('quantity');
        }

        $totalReturnedCups = $invoices->sum('returned_trays');
        $missingCups = $totalDeliveredCups - $totalReturnedCups;

        return view('reports.index', compact('invoices', 'customers', 'request', 'totalRevenue', 'totalDeliveredCups', 'totalReturnedCups', 'missingCups'));
    }

    private function generateExcel2($invoices)
    {
        // Identify all dynamic products in this specific filtered dataset
        $productNames = [];
        foreach ($invoices as $inv) {
            foreach ($inv->items as $item) {
                if (!in_array($item->item_name, $productNames)) {
                    $productNames[] = $item->item_name;
                }
            }
        }
        sort($productNames); // Alphabetize the columns

        // Initialize arrays for Week 1 to Week 5
        $weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $cupsData = [];
        $salesData = [];

        foreach ($weeks as $week) {
            $salesData[$week] = 0;
            foreach ($productNames as $pName) {
                $cupsData[$week][$pName] = 0;
            }
        }

        // Map the data into the week buckets based on Communion Date
        foreach ($invoices as $inv) {
            if (!$inv->communion_date) continue;

            $date = Carbon::parse($inv->communion_date);
            $weekNum = ceil($date->day / 7);
            $weekKey = 'Week ' . ($weekNum > 5 ? 5 : $weekNum); // Cap at max Week 5

            // Add Sales
            $salesData[$weekKey] += $inv->total_amount;

            // Add Cups
            foreach ($inv->items as $item) {
                $cupsData[$weekKey][$item->item_name] += $item->quantity;
            }
        }

        $timestamp = now()->format('Y_m_d_His');
        return Excel::download(new \App\Exports\WeeklySalesExport($productNames, $weeks, $cupsData, $salesData), "Sales_Report_{$timestamp}.xlsx");
    }
}
