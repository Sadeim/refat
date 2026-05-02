<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name_ar'] ?? null)) {
            return null;
        }

        return new Customer([
            'name_ar' => $row['name_ar'],
            'name_en' => $row['name_en'] ?? null,
            'type' => $row['type'] ?? 'individual',
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'address' => $row['address'] ?? null,
            'contact_person' => $row['contact_person'] ?? null,
            'contact_phone' => $row['contact_phone'] ?? null,
            'tax_id' => $row['tax_id'] ?? null,
            'contract_start' => $row['contract_start'] ?? null,
            'contract_end' => $row['contract_end'] ?? null,
            'contract_value' => (float) ($row['contract_value'] ?? 0),
            'status' => $row['status'] ?? 'active',
        ]);
    }
}
