<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ContactsImport implements ToCollection, WithHeadingRow
{
    public $headers;
    public $data;

    public function collection(Collection $rows)
    {
        $this->headers = $rows->first()->keys()->toArray();
        $this->data = $rows;
    }
}
