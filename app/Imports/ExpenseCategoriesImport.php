<?php

namespace App\Imports;

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpenseCategoriesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) return null;
        return ExpenseCategory::firstOrCreate(['name' => trim($row['name']), 'branch_id' => Auth::user()->branch_id]);
    }
}
