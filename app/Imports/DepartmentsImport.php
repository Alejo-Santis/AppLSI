<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class DepartmentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Log para debug (opcional, puedes comentarlo después)
        Log::info('Importando departamento:', $row);

        // Normalizar keys (remover espacios, convertir a minúsculas)
        $row = array_change_key_case($row, CASE_LOWER);
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        // Mapear campos del Excel (en inglés) a la base de datos
        return new Department([
            'code' => !empty($row['code']) ? $row['code'] : Department::generateCode(),
            'name' => $row['name'] ?? null,
            'description' => $row['description'] ?? null,
            'budget' => !empty($row['budget']) ? (float) str_replace(',', '', $row['budget']) : 0,
            'is_active' => $this->parseBoolean($row['status'] ?? 'active'),
            'manager_id' => !empty($row['manager_id']) ? (int) $row['manager_id'] : null,
        ]);
    }

    /**
     * Parsear valores booleanos o estados
     */
    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));

        // Valores que representan "activo" o "true"
        $trueValues = ['active', 'activo', 'yes', 'si', 'sí', '1', 'true', 'verdadero'];

        return in_array($value, $trueValues);
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.code' => 'nullable|string|max:50|unique:departments,code',
            '*.budget' => 'nullable|numeric|min:0',
            '*.manager_id' => 'nullable|exists:employees,id',
            '*.status' => 'nullable|in:active,inactive,activo,inactivo',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'El campo "name" es obligatorio en la fila :row',
            '*.name.string' => 'El campo "name" debe ser texto en la fila :row',
            '*.name.max' => 'El campo "name" no debe superar 255 caracteres en la fila :row',
            '*.code.unique' => 'El código ya existe en la base de datos (fila :row)',
            '*.code.max' => 'El código no debe superar 50 caracteres en la fila :row',
            '*.budget.numeric' => 'El presupuesto debe ser un número en la fila :row',
            '*.budget.min' => 'El presupuesto no puede ser negativo en la fila :row',
            '*.manager_id.exists' => 'El manager_id no existe en la base de datos (fila :row)',
            '*.status.in' => 'El status debe ser "active" o "inactive" en la fila :row',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'name' => 'nombre',
            'code' => 'código',
            'budget' => 'presupuesto',
            'manager_id' => 'ID del manager',
            'status' => 'estado',
        ];
    }
}
