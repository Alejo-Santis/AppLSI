<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Employee::select(
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'hire_date',
            'salary',
            'status',
            'birth_date',
            'address',
            'position_id',
            'department_id',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Apellido',
            'Correo',
            'Teléfono',
            'Fecha de Contratación',
            'Salario',
            'Estado',
            'Fecha de Nacimiento',
            'Dirección',
            'Cargo (ID)',
            'Departamento (ID)',
            'Fecha de Creación'
        ];
    }
}
