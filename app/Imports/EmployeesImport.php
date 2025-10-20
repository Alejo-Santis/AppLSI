<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Log para debug
        Log::info('Importando empleado:', $row);

        // Normalizar keys
        $row = array_change_key_case($row, CASE_LOWER);
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        return new Employee([
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'hire_date' => $this->parseDate($row['hire_date'] ?? null),
            'salary' => !empty($row['salary']) ? (float) str_replace(',', '', $row['salary']) : 0,
            'status' => $this->normalizeStatus($row['status'] ?? 'active'),
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'address' => $row['address'] ?? null,
            'photo_url' => null, // Las fotos no se importan por Excel
            'position_id' => !empty($row['position_id']) ? (int) $row['position_id'] : null,
            'department_id' => !empty($row['department_id']) ? (int) $row['department_id'] : null,
        ]);
    }

    /**
     * Parsear fechas en diferentes formatos
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Si es un número de Excel (serial date)
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            // Intentar parsear como fecha
            return Carbon::parse($date);
        } catch (\Exception $e) {
            Log::warning('Error parseando fecha: ' . $date, ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Normalizar estado del empleado
     */
    private function normalizeStatus($status): string
    {
        $status = strtolower(trim($status));

        $validStatuses = ['active', 'inactive', 'on_leave', 'terminated'];

        // Mapeo de valores comunes
        $statusMap = [
            'activo' => 'active',
            'inactivo' => 'inactive',
            'licencia' => 'on_leave',
            'en licencia' => 'on_leave',
            'terminado' => 'terminated',
            'despedido' => 'terminated',
        ];

        $normalized = $statusMap[$status] ?? $status;

        return in_array($normalized, $validStatuses) ? $normalized : 'active';
    }

    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string|max:255',
            '*.last_name' => 'required|string|max:255',
            '*.email' => 'required|email|unique:employees,email',
            '*.phone' => 'nullable|string|max:20',
            '*.hire_date' => 'required|date',
            '*.salary' => 'required|numeric|min:0',
            '*.status' => 'nullable|in:active,inactive,on_leave,terminated,activo,inactivo,licencia,terminado',
            '*.birth_date' => 'nullable|date|before:today',
            '*.position_id' => 'nullable|exists:positions,id',
            '*.department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.first_name.required' => 'El nombre es obligatorio en la fila :row',
            '*.first_name.max' => 'El nombre no debe superar 255 caracteres en la fila :row',
            '*.last_name.required' => 'El apellido es obligatorio en la fila :row',
            '*.last_name.max' => 'El apellido no debe superar 255 caracteres en la fila :row',
            '*.email.required' => 'El email es obligatorio en la fila :row',
            '*.email.email' => 'El email debe ser válido en la fila :row',
            '*.email.unique' => 'Este email ya existe en la base de datos (fila :row)',
            '*.phone.max' => 'El teléfono no debe superar 20 caracteres en la fila :row',
            '*.hire_date.required' => 'La fecha de contratación es obligatoria en la fila :row',
            '*.hire_date.date' => 'La fecha de contratación debe ser una fecha válida en la fila :row',
            '*.salary.required' => 'El salario es obligatorio en la fila :row',
            '*.salary.numeric' => 'El salario debe ser un número en la fila :row',
            '*.salary.min' => 'El salario no puede ser negativo en la fila :row',
            '*.status.in' => 'El status debe ser: active, inactive, on_leave o terminated (fila :row)',
            '*.birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida en la fila :row',
            '*.birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy (fila :row)',
            '*.position_id.exists' => 'El position_id no existe en la base de datos (fila :row)',
            '*.department_id.exists' => 'El department_id no existe en la base de datos (fila :row)',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'email',
            'phone' => 'teléfono',
            'hire_date' => 'fecha de contratación',
            'salary' => 'salario',
            'status' => 'estado',
            'birth_date' => 'fecha de nacimiento',
            'position_id' => 'ID del puesto',
            'department_id' => 'ID del departamento',
        ];
    }
}
