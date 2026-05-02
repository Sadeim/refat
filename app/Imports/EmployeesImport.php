<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name_ar'] ?? null)) {
            return null;
        }

        return new Employee([
            'name_ar' => $row['name_ar'],
            'name_en' => $row['name_en'] ?? null,
            'national_id' => $row['national_id'] ?? null,
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'position' => $row['position'] ?? null,
            'department' => $row['department'] ?? null,
            'start_date' => $row['start_date'] ?? null,
            'daily_hours' => (int) ($row['daily_hours'] ?? 8),
            'basic_salary' => (float) ($row['basic_salary'] ?? 0),
            'allowances' => (float) ($row['allowances'] ?? 0),
            'status' => $row['status'] ?? 'active',
        ]);
    }
}
