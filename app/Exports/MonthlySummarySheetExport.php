<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class MonthlySummarySheetExport extends WeeklySheetExport
{
    public function __construct($invoices, $month)
    {
        $this->invoices = $invoices;
        $this->month = $month;
        $this->week = 'MONTHLY SUMMARY';
    }

    public function title(): string
    {
        return 'Monthly Summary';
    }

    public function view(): View
    {
        // 1. Get the standard big table data
        $reportData = $this->buildReportData($this->invoices);

        // 2. Calculate Weekly Summaries for the new tables
        $weeks = [1, 2, 3, 4, 5];
        $weeklySummary = [];
        $productNames = array_keys($reportData['products']);

        $grandTotalSales = 0;
        $grandTotalCups = 0;
        $productTotals = array_fill_keys($productNames, 0);

        foreach ($weeks as $week) {
            $weekInvoices = $this->invoices->where('communion_week', $week);

            $weekSales = $weekInvoices->sum('total_amount');
            $grandTotalSales += $weekSales;

            $weekProducts = array_fill_keys($productNames, 0);
            foreach ($weekInvoices as $inv) {
                foreach ($inv->items as $item) {
                    $weekProducts[$item->item_name] += $item->quantity;
                    $productTotals[$item->item_name] += $item->quantity;
                    $grandTotalCups += $item->quantity;
                }
            }

            $weeklySummary["WEEK $week"] = [
                'sales' => $weekSales,
                'products' => $weekProducts
            ];
        }

        return view('exports.sales_sheet', [
            'title' => 'MONTHLY SUMMARY',
            'date' => Carbon::parse($this->month)->format('F Y'),
            'products' => $reportData['products'],
            'rows' => $reportData['rows'],
            'totals' => $reportData['totals'],

            // --- VARIABLES FOR SUMMARY TABLES ---
            'weeklySummary' => $weeklySummary,
            'productNames' => $productNames,
            'productTotals' => $productTotals,
            'grandTotalSales' => $grandTotalSales,
            'grandTotalCups' => $grandTotalCups,
        ]);
    }
}
