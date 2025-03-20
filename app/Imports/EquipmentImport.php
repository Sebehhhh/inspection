<?php
namespace App\Imports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EquipmentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Equipment([
            'id'          => $row['id'], // Menggunakan ID dari file Excel
            'name'        => $row['name'],
            'description' => $row['description'],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}