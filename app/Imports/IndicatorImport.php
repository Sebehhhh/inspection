<?php
namespace App\Imports;

use App\Models\Indicator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndicatorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Indicator([
            'equipment_id' => $row['equipment_id'],
            'name'         => $row['name'],
            'unit'         => $row['unit'],
            'baseline'     => $row['baseline'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}