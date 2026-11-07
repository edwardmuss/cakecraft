<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WeeklySalesExport implements FromView, ShouldAutoSize
{
    public $productNames;
    public $weeks;
    public $cupsData;
    public $salesData;

    public function __construct($productNames, $weeks, $cupsData, $salesData)
    {
        $this->productNames = $productNames;
        $this->weeks = $weeks;
        $this->cupsData = $cupsData;
        $this->salesData = $salesData;
    }

    public function view(): View
    {
        return view('reports.exports.weekly_sales', [
            'productNames' => $this->productNames,
            'weeks' => $this->weeks,
            'cupsData' => $this->cupsData,
            'salesData' => $this->salesData,
        ]);
    }
}
