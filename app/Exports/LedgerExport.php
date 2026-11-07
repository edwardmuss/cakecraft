<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LedgerExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ["Date", "Reference", "Event Type", "Amount (KES)", "Balance Before", "Balance After", "Source", "Description"];
    }

    public function map($entry): array
    {
        return [
            $entry->created_at->format('Y-m-d H:i:s'),
            $entry->reference,
            strtoupper($entry->event_type),
            number_format($entry->amount_minor / 100, 2),
            number_format($entry->balance_before_minor / 100, 2),
            number_format($entry->balance_after_minor / 100, 2),
            $entry->source,
            $entry->description,
        ];
    }

    /**
     * Styles the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Get total number of rows (including header)
        $lastRow = $sheet->getHighestRow();
        $lastColumn = 'H'; // Columns A through H

        // Style the Header Row (Row 1)
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '405189'], // Velzon Dark Blue
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Add Borders to all data cells
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);

        // Conditional Formatting: Right-align currency columns (D, E, F)
        $sheet->getStyle("D2:F{$lastRow}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Zebra Striping (Optional but looks great)
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:{$lastColumn}{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }
        }

        return [];
    }
}
