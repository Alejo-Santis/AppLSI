<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Project([
            'name'               => $row['name'] ?? $row['nombre'] ?? null,
            'code'               => $row['code'] ?? $row['codigo'] ?? null,
            'description'        => $row['description'] ?? $row['descripcion'] ?? null,
            'start_date'         => $row['start_date'] ?? $row['fecha_inicio'] ?? null,
            'end_date'           => $row['end_date'] ?? $row['fecha_fin'] ?? null,
            'status'             => $row['status'] ?? $row['estado'] ?? 'planning',
            'budget'             => $row['budget'] ?? $row['presupuesto'] ?? null,
            'project_manager_id' => $row['project_manager_id'] ?? $row['gerente_id'] ?? null,
        ]);
    }
}
