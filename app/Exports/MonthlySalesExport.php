<?php

namespace App\Exports;

use App\Exports\MonthlySummarySheetExport;
use App\Exports\WeeklySheetExport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlySalesExport implements WithMultipleSheets
{
    use Exportable;

    protected $invoices;
    protected $month;

    public function __construct($invoices, $month)
    {
        $this->invoices = $invoices;
        $this->month = $month;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Generate a sheet for Weeks 1 through 5
        for ($week = 1; $week <= 5; $week++) {
            $weekInvoices = $this->invoices->where('communion_week', $week);
            if ($weekInvoices->count() > 0) {
                $sheets[] = new WeeklySheetExport($weekInvoices, $this->month, $week);
            }
        }

        // Generate the Monthly Totals Sheet
        $sheets[] = new MonthlySummarySheetExport($this->invoices, $this->month);

        return $sheets;
    }
}
