<?php

namespace App\Exports;

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseCategoriesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ExpenseCategory::where('branch_id', Auth::user()->branch_id)->select('name')->get();
    }
    public function headings(): array
    {
        return ['Name'];
    }
}
