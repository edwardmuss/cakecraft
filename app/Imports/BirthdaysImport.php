<?php

namespace App\Imports;

use App\Models\Birthday;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BirthdaysImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                // Skip the first row if it's a header
                continue;
            }

            // Validate required columns before processing
            if (isset($row[0], $row[1], $row[2], $row[3])) {
                try {
                    // Check if the date is in Excel's numeric format
                    if (is_numeric($row[3])) {
                        // Convert Excel numeric date to a readable format
                        $date = Carbon::instance(Date::excelToDateTimeObject($row[3]))->format('Y-m-d');
                    } else {
                        // Otherwise, parse the date from the string format
                        $date = Carbon::createFromFormat('Y-m-d', $row[3])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // Log or handle invalid date format errors
                    throw new \Exception("Invalid date format at row $index: " . $row[3]);
                }

                // Save birthday to the database
                Birthday::create([
                    'branch_id' => Auth::user()->branch_id, // Ensure the birthdays are linked to the account
                    'name' => $row[0],
                    'phone' => formatPhoneNumber($row[1]),
                    'type' => strtolower($row[2]), // Ensure it's either 'birthday' or 'anniversary'
                    'date' => $date, // Save the formatted date
                ]);
            } else {
                // Handle missing required columns or values
                throw new \Exception("Missing required data at row $index");
            }
        }
    }
}
