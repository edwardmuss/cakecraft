<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $branch_id;

    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    public function query()
    {
        return Product::query()->where('branch_id', $this->branch_id)->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Description',
            'Unit Price (KES)',
            'Tracks Stock?',
            'Current Stock',
            'Reorder Level',
            'Status'
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->description ?? 'N/A',
            number_format($product->unit_price, 2, '.', ''),
            $product->track_stock ? 'Yes' : 'No',
            $product->track_stock ? $product->current_stock : 'N/A',
            $product->track_stock ? $product->reorder_level : 'N/A',
            $product->is_active ? 'Active' : 'Inactive',
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
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Apply bold text and background color to Header (Row 1)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'] // White text
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF405189'] // Dark Blue/Primary color
            ],
        ]);
    }
}
