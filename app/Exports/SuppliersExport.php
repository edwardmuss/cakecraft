<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Supplier::where('branch_id', Auth::user()->branch_id)->select('name', 'phone', 'email', 'kra_pin', 'address')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Phone', 'Email', 'KRA PIN', 'Address'];
    }
}
