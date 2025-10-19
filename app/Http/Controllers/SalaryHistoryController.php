<?php

namespace App\Http\Controllers;

use App\Models\SalaryHistory;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SweetAlert2\Laravel\Swal;

class SalaryHistoryController extends Controller
{
    /**
     * Display a listing of salary history for an employee.
     */
    public function history(Employee $employee)
    {
        $history = $employee->salaryHistories()
            ->with('approver')
            ->orderBy('change_date', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'previous_salary' => $record->previous_salary,
                    'new_salary' => $record->new_salary,
                    'salary_difference' => $record->salary_difference,
                    'percentage_change' => round($record->percentage_change, 2),
                    'change_type' => $record->change_type,
                    'change_date' => $record->change_date->format('Y-m-d'),
                    'change_date_formatted' => $record->change_date->format('d/m/Y'),
                    'change_date_human' => $record->change_date->diffForHumans(),
                    'reason' => $record->reason,
                    'approver' => $record->approver ? $record->approver->name : 'Sistema',
                ];
            });

        return response()->json($history);
    }

    /**
     * Store a newly created salary change.
     */
    public function storeHistory(Request $request, Employee $employee)
    {
        try {
            $validated = $request->validate([
                'new_salary' => ['required', 'numeric', 'min:0'],
                'reason' => ['required', 'string'],
                'change_date' => ['required', 'date'],
            ], [
                'new_salary.required' => 'El nuevo salario es obligatorio',
                'new_salary.numeric' => 'El salario debe ser un número válido',
                'new_salary.min' => 'El salario no puede ser negativo',
                'reason.required' => 'La razón del cambio es obligatoria',
                'change_date.required' => 'La fecha de cambio es obligatoria',
                'change_date.date' => 'La fecha no es válida',
            ]);

            // Obtener el salario actual del empleado
            $previousSalary = $employee->salary;

            // Validar que el nuevo salario sea diferente
            if ($validated['new_salary'] == $previousSalary) {
                Swal::warning([
                    'title' => 'Advertencia',
                    'text' => 'El nuevo salario debe ser diferente al actual',
                    'icon' => 'warning',
                ]);
                return back()->withInput();
            }

            // Crear registro en historial
            SalaryHistory::create([
                'employee_id' => $employee->id,
                'previous_salary' => $previousSalary,
                'new_salary' => $validated['new_salary'],
                'change_date' => $validated['change_date'],
                'reason' => $validated['reason'],
                'approved_by' => Auth::id(),
            ]);

            // Actualizar el salario del empleado
            $employee->update(['salary' => $validated['new_salary']]);

            Swal::success([
                'title' => 'Actualizado',
                'text' => 'Cambio salarial actualizado exitosamente.',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Registrar',
                'text' => 'No se pudo registrar el cambio salarial: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    /**
     * Remove the specified salary history record.
     */
    public function destroyHistory(Employee $employee, SalaryHistory $history)
    {
        try {
            // Verificar que el historial pertenece al empleado
            if ($history->employee_id !== $employee->id) {
                abort(403, 'No autorizado');
            }

            // No permitir eliminar si es el último registro
            $totalRecords = SalaryHistory::where('employee_id', $employee->id)->count();

            if ($totalRecords <= 1) {
                Swal::warning([
                    'title' => 'No se puede eliminar',
                    'text' => 'No puedes eliminar el único registro de historial salarial',
                    'icon' => 'warning',
                ]);
                return back();
            }

            // Verificar que no sea el registro más reciente (que define el salario actual)
            $latestRecord = SalaryHistory::where('employee_id', $employee->id)
                ->orderBy('change_date', 'desc')
                ->first();

            if ($history->id === $latestRecord->id) {
                Swal::warning([
                    'title' => 'No se puede eliminar',
                    'text' => 'No puedes eliminar el cambio salarial más reciente. Este define el salario actual del empleado.',
                    'icon' => 'warning',
                ]);
                return back();
            }

            // Eliminar registro
            $history->delete();

            Swal::success([
                'title' => 'Eliminado',
                'text' => 'Registro eliminado exitosamente.',
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Eliminar',
                'text' => 'No se pudo eliminar el registro: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back();
        }
    }

    /**
     * Get change reasons for select
     */
    public function getChangeReasons()
    {
        return response()->json(SalaryHistory::getChangeReasons());
    }
}
