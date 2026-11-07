<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $branch_id;

    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    public function query()
    {
        return Customer::query()->where('branch_id', $this->branch_id)->orderBy('organization_name', 'asc');
    }

    public function headings(): array
    {
        return [
            'Name',
            'Organization',
            'Phone',
            'Email',
            'Billing Address',
            'Delivery Location',
            'Date Added',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->organization_name ?? 'N/A',
            $customer->phone,
            $customer->email ?? 'N/A',
            $customer->billing_address ?? 'N/A',
            $customer->delivery_location ?? 'N/A',
            $customer->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $range = "A1:{$lastColumn}{$lastRow}";

        // Apply borders to all cells
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Black borders
                ],
            ],
        ]);

        // Apply bold text and a nice Blue background color to the Header (Row 1)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF3577F1'], // Soft Blue
            ],
        ]);
    }
}
