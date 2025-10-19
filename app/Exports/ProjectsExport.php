<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Project::select(
            'id',
            'name',
            'code',
            'description',
            'start_date',
            'end_date',
            'status',
            'budget',
            'project_manager_id',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Código',
            'Descripción',
            'Fecha de Inicio',
            'Fecha de Fin',
            'Estado',
            'Presupuesto',
            'Gerente de Proyecto (ID)',
            'Fecha de Creación',
        ];
    }
}
