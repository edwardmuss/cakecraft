<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $branch_id;
    protected $startDate;
    protected $endDate;

    public function __construct($branch_id, $startDate, $endDate)
    {
        $this->branch_id = $branch_id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return Payment::query()
            ->with(['invoice.customer'])
            ->where('branch_id', $this->branch_id)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Receipt Ref',
            'Invoice #',
            'Customer Name',
            'Payment Method',
            'Amount (KES)',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->created_at->format('Y-m-d H:i A'),
            $payment->reference,
            $payment->invoice->invoice_number ?? 'N/A',
            $payment->invoice->customer->name ?? 'N/A',
            $payment->method,
            number_format($payment->amount, 2, '.', ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $range = "A1:{$lastColumn}{$lastRow}";

        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Use a nice Green background for payments to differentiate from products
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0AB39C'] // Success Green
            ],
        ]);
    }
}
