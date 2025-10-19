<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'first_name'    => $row['first_name'] ?? $row['nombre'] ?? null,
            'last_name'     => $row['last_name'] ?? $row['apellido'] ?? null,
            'email'         => $row['email'] ?? $row['correo'] ?? null,
            'phone'         => $row['phone'] ?? $row['telefono'] ?? null,
            'hire_date'     => $row['hire_date'] ?? $row['fecha_contratacion'] ?? null,
            'salary'        => $row['salary'] ?? $row['salario'] ?? null,
            'status'        => $row['status'] ?? 'active',
            'birth_date'    => $row['birth_date'] ?? $row['fecha_nacimiento'] ?? null,
            'address'       => $row['address'] ?? $row['direccion'] ?? null,
            'position_id'   => $row['position_id'] ?? $row['cargo_id'] ?? null,
            'department_id' => $row['department_id'] ?? $row['departamento_id'] ?? null,
        ]);
    }
}
