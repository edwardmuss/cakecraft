<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class WeeklySheetExport implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $invoices;
    protected $month;
    protected $week;

    public function __construct($invoices, $month, $week)
    {
        $this->invoices = $invoices;
        $this->month = $month;
        $this->week = $week;
    }

    public function title(): string
    {
        return 'Week ' . $this->week;
    }

    public function view(): View
    {
        $reportData = $this->buildReportData($this->invoices);

        return view('exports.sales_sheet', [
            'title' => 'WEEK ' . $this->week,
            'date' => Carbon::parse($this->month)->format('F Y'),
            'products' => $reportData['products'],
            'rows' => $reportData['rows'],
            'totals' => $reportData['totals'],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Add borders and background colors matching your screenshot
        return [
            1 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F4B084']]], // Orange Header
        ];
    }

    // This helper method groups data by Church so it works perfectly for both Weeks and Months!
    protected function buildReportData($invoices)
    {
        $products = [];
        $rows = [];
        $totals = [
            'issued' => 0,
            'returned' => 0,
            'delivery' => 0,
            'total' => 0,
            'paid' => 0,
            'balance' => 0,
            'items' => []
        ];

        // 1. Identify all unique products in this dataset and their unit prices
        foreach ($invoices as $inv) {
            foreach ($inv->items as $item) {
                if (!isset($products[$item->item_name])) {
                    $products[$item->item_name] = $item->unit_price;
                    $totals['items'][$item->item_name] = ['qty' => 0, 'amount' => 0];
                }
            }
        }
        ksort($products); // Alphabetize columns

        // 2. Group invoices by Customer
        $groupedInvoices = $invoices->groupBy('customer_id');

        foreach ($groupedInvoices as $customerId => $customerInvoices) {
            $customer = $customerInvoices->first()->customer;

            // Calculate Trays (Recyclable only)
            $traysIssued = $customerInvoices->sum(function ($inv) {
                return $inv->items->whereIn('tray_type', ['recyclable', 'rec'])->sum('tray_count');
            });
            $traysReturned = $customerInvoices->sum('returned_trays');

            $delivery = $customerInvoices->sum('shipping_fee');
            $total = $customerInvoices->sum('total_amount');
            $paid = $customerInvoices->sum('amount_paid');
            $balance = $total - $paid;

            // Aggregate items
            $itemQtys = [];
            $itemAmounts = [];
            foreach ($products as $pName => $price) {
                $itemQtys[$pName] = 0;
                $itemAmounts[$pName] = 0;
            }

            foreach ($customerInvoices as $inv) {
                foreach ($inv->items as $item) {
                    $itemQtys[$item->item_name] += $item->quantity;
                    $itemAmounts[$item->item_name] += ($item->quantity * $item->unit_price);

                    // Add to grand totals
                    $totals['items'][$item->item_name]['qty'] += $item->quantity;
                    $totals['items'][$item->item_name]['amount'] += ($item->quantity * $item->unit_price);
                }
            }

            $rows[] = [
                'church' => $customer->organization_name ?? $customer->name,
                'contacts' => $customer->phone,
                'issued' => $traysIssued,
                'returned' => $traysReturned,
                'delivery' => $delivery,
                'total' => $total,
                'paid' => $paid,
                'balance' => $balance,
                'itemQtys' => $itemQtys,
                'itemAmounts' => $itemAmounts,
            ];

            // Add to grand totals
            $totals['issued'] += $traysIssued;
            $totals['returned'] += $traysReturned;
            $totals['delivery'] += $delivery;
            $totals['total'] += $total;
            $totals['paid'] += $paid;
            $totals['balance'] += $balance;
        }

        return ['products' => $products, 'rows' => $rows, 'totals' => $totals];
    }
}
