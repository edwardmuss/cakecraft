<?php

namespace App\Imports;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ExpensesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['date']) || empty($row['category'])) {
            return null;
        }

        $branchId = Auth::user()->branch_id;

        // 1. Dynamic Category Creation
        $category = ExpenseCategory::firstOrCreate(
            ['name' => trim($row['category']), 'branch_id' => $branchId]
        );

        // 2. Dynamic Supplier Creation
        $supplierId = null;
        if (!empty($row['supplier'])) {
            $supplier = Supplier::firstOrCreate(
                ['name' => trim($row['supplier']), 'branch_id' => $branchId]
            );
            $supplierId = $supplier->id;
        }

        $beforeTax = floatval($row['before_tax'] ?? 0);
        $tax = floatval($row['tax_vat'] ?? 0);

        // 3. Format Date safely
        try {
            $date = Carbon::parse($row['date'])->format('Y-m-d');
        } catch (\Exception $e) {
            $date = now()->format('Y-m-d');
        }

        return new Expense([
            'branch_id'           => $branchId,
            'supplier_id'         => $supplierId,
            'expense_category_id' => $category->id,
            'expense_date'        => $date,
            'reference_number'    => $row['ref_number'] ?? null,
            'amount_before_tax'   => $beforeTax,
            'tax_amount'          => $tax,
            'total_amount'        => $beforeTax + $tax,
            'payment_method'      => $row['payment_method'] ?? 'Cash',
            'notes'               => $row['notes'] ?? null,
            'created_by'          => Auth::user()->id,
        ]);
    }
}
