<?php

namespace App\Imports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PositionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Position([
            'title'       => $row['title'] ?? null,
            'description' => $row['description'] ?? null,
            'level'       => $row['level'] ?? 'junior',
            'min_salary'  => $row['min_salary'] ?? 0,
            'max_salary'  => $row['max_salary'] ?? 0,
            'is_active'   => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ]);
    }
}
