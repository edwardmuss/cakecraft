<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $branchId;
    protected $startDate;
    protected $endDate;
    protected $supplierId;
    protected $categoryId;

    public function __construct($branchId, $startDate, $endDate, $supplierId = null, $categoryId = null)
    {
        $this->branchId = $branchId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->supplierId = $supplierId;
        $this->categoryId = $categoryId;
    }

    public function collection()
    {
        $query = Expense::with(['supplier', 'category', 'creator'])
            ->where('branch_id', $this->branchId)
            ->whereBetween('expense_date', [$this->startDate, $this->endDate]);

        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }
        if ($this->categoryId) {
            $query->where('expense_category_id', $this->categoryId);
        }

        return $query->orderBy('expense_date', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Expense Date',
            'Ref #',
            'Category',
            'Supplier / Vendor',
            'Amount Before Tax (KES)',
            'Tax / VAT (KES)',
            'Total Amount (KES)',
            'Payment Method',
            'Notes',
            'Recorded By'
        ];
    }

    public function map($expense): array
    {
        return [
            \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y'),
            $expense->reference_number ?? 'N/A',
            $expense->category->name ?? 'N/A',
            $expense->supplier->name ?? 'None',
            $expense->amount_before_tax,
            $expense->tax_amount,
            $expense->total_amount,
            $expense->payment_method,
            $expense->notes,
            $expense->creator->name ?? 'System'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the dynamic range of the sheet (e.g., "A1:J25")
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $fullRange = 'A1:' . $highestColumn . $highestRow;

        // Apply thin borders to the entire range
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Black borders
                ],
            ],
        ]);

        // Keep your existing header styling (Bold headings)
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
