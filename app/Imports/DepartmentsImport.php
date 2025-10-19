<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepartmentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Department([
            'code' => $row['codigo'] ?? Department::generateCode(),
            'name' => $row['nombre'] ?? null,
            'description' => $row['descripcion'] ?? null,
            'budget' => $row['presupuesto'] ?? 0,
            'is_active' => isset($row['activo']) ? (bool)$row['activo'] : true,
            'manager_id' => $row['id_del_manager'] ?? null,
        ]);
    }
}
