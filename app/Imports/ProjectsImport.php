<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProjectsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Log ANTES de normalizar
        Log::info('ROW ORIGINAL:', $row);

        // Normalizar keys
        $row = array_change_key_case($row, CASE_LOWER);
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        // Log DESPUÉS de normalizar
        Log::info('ROW NORMALIZADO:', $row);

        // Validar campos requeridos
        if (empty($row['name'])) {
            Log::error('FALTA EL CAMPO name');
            return null; // Skip this row
        }

        if (empty($row['start_date'])) {
            Log::error('FALTA EL CAMPO start_date');
            return null; // Skip this row
        }

        $project = new Project([
            'name' => $row['name'],
            'code' => !empty($row['code']) ? $row['code'] : Project::generateCode(),
            'description' => $row['description'] ?? null,
            'start_date' => $this->parseDate($row['start_date']),
            'end_date' => $this->parseDate($row['end_date'] ?? null),
            'status' => $this->normalizeStatus($row['status'] ?? 'planning'),
            'budget' => !empty($row['budget']) ? (float) str_replace(',', '', $row['budget']) : null,
            'project_manager_id' => !empty($row['project_manager_id']) ? (int) $row['project_manager_id'] : null,
        ]);

        Log::info('PROYECTO CREADO:', $project->toArray());

        return $project;
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
     * Normalizar estado del proyecto
     */
    private function normalizeStatus($status): string
    {
        $status = strtolower(trim($status));

        $validStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];

        // Mapeo de valores comunes
        $statusMap = [
            'planeacion' => 'planning',
            'en planeacion' => 'planning',
            'activo' => 'active',
            'pausa' => 'on_hold',
            'en pausa' => 'on_hold',
            'pausado' => 'on_hold',
            'completado' => 'completed',
            'terminado' => 'completed',
            'finalizado' => 'completed',
            'cancelado' => 'cancelled',
        ];

        $normalized = $statusMap[$status] ?? $status;

        return in_array($normalized, $validStatuses) ? $normalized : 'planning';
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.code' => 'nullable|string|max:50|unique:projects,code',
            '*.start_date' => 'required|date',
            '*.end_date' => 'nullable|date|after_or_equal:*.start_date',
            '*.status' => 'nullable|in:planning,active,on_hold,completed,cancelled,planeacion,activo,pausa,pausado,completado,terminado,cancelado',
            '*.budget' => 'nullable|numeric|min:0',
            '*.project_manager_id' => 'nullable|exists:employees,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'El nombre del proyecto es obligatorio en la fila :row',
            '*.name.max' => 'El nombre no debe superar 255 caracteres en la fila :row',
            '*.code.unique' => 'El código del proyecto ya existe en la base de datos (fila :row)',
            '*.code.max' => 'El código no debe superar 50 caracteres en la fila :row',
            '*.start_date.required' => 'La fecha de inicio es obligatoria en la fila :row',
            '*.start_date.date' => 'La fecha de inicio debe ser una fecha válida en la fila :row',
            '*.end_date.date' => 'La fecha de fin debe ser una fecha válida en la fila :row',
            '*.end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio (fila :row)',
            '*.status.in' => 'El estado debe ser: planning, active, on_hold, completed o cancelled (fila :row)',
            '*.budget.numeric' => 'El presupuesto debe ser un número en la fila :row',
            '*.budget.min' => 'El presupuesto no puede ser negativo en la fila :row',
            '*.project_manager_id.exists' => 'El project_manager_id no existe en la base de datos (fila :row)',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'name' => 'nombre',
            'code' => 'código',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'status' => 'estado',
            'budget' => 'presupuesto',
            'project_manager_id' => 'ID del gerente',
        ];
    }
}
