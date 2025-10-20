<?php

namespace App\Imports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class PositionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Log para debug
        Log::info('Importando puesto:', $row);

        // Normalizar keys
        $row = array_change_key_case($row, CASE_LOWER);
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        return new Position([
            'title' => $row['title'] ?? null,
            'description' => $row['description'] ?? null,
            'level' => $this->normalizeLevel($row['level'] ?? 'junior'),
            'min_salary' => !empty($row['min_salary']) ? (float) str_replace(',', '', $row['min_salary']) : 0,
            'max_salary' => !empty($row['max_salary']) ? (float) str_replace(',', '', $row['max_salary']) : 0,
            'is_active' => $this->parseBoolean($row['status'] ?? 'active'),
        ]);
    }

    /**
     * Normalizar nivel del puesto
     */
    private function normalizeLevel($level): string
    {
        $level = strtolower(trim($level));

        $validLevels = ['junior', 'mid', 'senior', 'lead', 'manager', 'director'];

        return in_array($level, $validLevels) ? $level : 'junior';
    }

    /**
     * Parsear valores booleanos
     */
    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        $trueValues = ['active', 'activo', 'yes', 'si', 'sí', '1', 'true', 'verdadero'];

        return in_array($value, $trueValues);
    }

    public function rules(): array
    {
        return [
            '*.title' => 'required|string|max:255',
            '*.level' => 'nullable|in:junior,mid,senior,lead,manager,director',
            '*.min_salary' => 'nullable|numeric|min:0',
            '*.max_salary' => 'nullable|numeric|min:0|gte:*.min_salary',
            '*.status' => 'nullable|in:active,inactive,activo,inactivo',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.title.required' => 'El campo "title" es obligatorio en la fila :row',
            '*.title.max' => 'El título no debe superar 255 caracteres en la fila :row',
            '*.level.in' => 'El nivel debe ser: junior, mid, senior, lead, manager o director (fila :row)',
            '*.min_salary.numeric' => 'El salario mínimo debe ser un número en la fila :row',
            '*.min_salary.min' => 'El salario mínimo no puede ser negativo en la fila :row',
            '*.max_salary.numeric' => 'El salario máximo debe ser un número en la fila :row',
            '*.max_salary.gte' => 'El salario máximo debe ser mayor o igual al mínimo en la fila :row',
            '*.status.in' => 'El status debe ser "active" o "inactive" en la fila :row',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'title' => 'título',
            'level' => 'nivel',
            'min_salary' => 'salario mínimo',
            'max_salary' => 'salario máximo',
            'status' => 'estado',
        ];
    }
}
