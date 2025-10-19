<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Department::select('id', 'code', 'name', 'description', 'budget', 'is_active', 'manager_id', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Nombre',
            'Descripción',
            'Presupuesto',
            'Activo',
            'ID del Manager',
            'Fecha de creación',
        ];
    }
}
