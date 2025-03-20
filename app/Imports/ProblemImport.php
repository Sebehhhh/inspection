<?php

namespace App\Imports;

use App\Models\Problem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProblemImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Problem([
            'id'                 => $row['id'],
            'equipment_id'       => $row['equipment_id'],
            'parent_problem_id'  => $row['parent_problem_id'],
            'name'               => $row['name'],
            'further_testing'    => $row['further_testing'],
            'corrective_action'  => $row['corrective_action'],
            'action_taken'       => $row['action_taken'],
            'possible_cause'     => $row['possible_cause'],
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }
}
