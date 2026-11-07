<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) return null;

        return Supplier::updateOrCreate(
            ['name' => trim($row['name']), 'branch_id' => Auth::user()->branch_id],
            [
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
                'kra_pin' => $row['kra_pin'] ?? null,
                'address' => $row['address'] ?? null,
            ]
        );
    }
}
