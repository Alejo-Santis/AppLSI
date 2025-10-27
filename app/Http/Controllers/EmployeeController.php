<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Imports\EmployeesImport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use SweetAlert2\Laravel\Swal;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function employees(Request $request)
    {
        $query = Employee::with(['department', 'position']);

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filtro por departamento
        if ($request->has('department_id') && $request->department_id !== 'all') {
            $query->where('department_id', $request->department_id);
        }

        // Filtro por puesto
        if ($request->has('position_id') && $request->position_id !== 'all') {
            $query->where('position_id', $request->position_id);
        }

        // Filtro por estado
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Ordenamiento
        $sortField = $request->get('sort_field', 'first_name');
        $sortDirection = $request->get('sort_direction', 'asc');

        if ($sortField === 'full_name') {
            $query->orderBy('first_name', $sortDirection)
                ->orderBy('last_name', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $employees = $query->paginate($request->get('per_page', 10))
            ->withQueryString();

        return Inertia::render('App/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::where('is_active', true)->select('id', 'name')->get(),
            'positions' => Position::where('is_active', true)->select('id', 'title')->get(),
            'filters' => $request->only(['search', 'department_id', 'position_id', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createEmployee()
    {
        return Inertia::render('App/Employees/Create', [
            'departments' => Department::where('is_active', true)
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get(),
            'positions' => Position::where('is_active', true)
                ->select('id', 'title', 'level')
                ->orderBy('title')
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeEmployee(StoreEmployeeRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('photo')) {
                $validated['photo_url'] = $request->file('photo')->store('employees/photos', 'public');
            }

            Employee::create($validated);

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Empleado creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('employees.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Crear!',
                'text' => 'No es posible crear el empleado'.$e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function showEmployeeDetails(Employee $employee)
    {
        $employee->load([
            'department',
            'position',
            'user',
            'documents' => fn ($q) => $q->orderBy('created_at', 'desc'),
            'emergencyContacts',
            'salaryHistories' => fn ($q) => $q->orderBy('change_date', 'desc'),
            'projectAssignments.project',
        ]);

        // Evitar errores si hire_date es nulo
        if (! $employee->hire_date) {
            $stats = [
                'years_of_service' => 0,
                'months_in_company' => 0,
                'total_documents' => $employee->documents->count(),
                'active_projects' => $employee->projectAssignments->where('is_active', true)->count(),
                'salary_changes' => $employee->salaryHistories->count(),
            ];
        } else {
            $hireDate = Carbon::parse($employee->hire_date)->startOfDay();
            $now = now()->startOfDay();

            // Usamos diff() para obtener años y meses exactos, no fraccionados
            $diff = $hireDate->diff($now);

            $stats = [
                'years_of_service' => (int) $diff->y,
                'months_in_company' => (int) ($diff->y * 12 + $diff->m),
                'total_documents' => $employee->documents->count(),
                'active_projects' => $employee->projectAssignments->where('is_active', true)->count(),
                'salary_changes' => $employee->salaryHistories->count(),
            ];
        }

        return Inertia::render('App/Employees/Show', [
            'employee' => $employee,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editEmployee(Employee $employee)
    {
        return Inertia::render('App/Employees/Edit', [
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'hire_date' => $employee->hire_date ? date('Y-m-d', strtotime($employee->hire_date)) : null,
                'salary' => $employee->salary,
                'status' => $employee->status,
                'birth_date' => $employee->birth_date ? date('Y-m-d', strtotime($employee->birth_date)) : null,
                'address' => $employee->address,
                'photo_url' => $employee->photo_url,
                'position_id' => $employee->position_id,
                'department_id' => $employee->department_id,
                $employee,
            ],
            'departments' => Department::where('is_active', true)
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get(),
            'positions' => Position::where('is_active', true)
                ->select('id', 'title', 'level')
                ->orderBy('title')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateEmployee(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            $validated = $request->validated();

            // Procesar foto si existe
            if ($request->hasFile('photo')) {
                // Eliminar foto anterior si existe
                if ($employee->photo_url) {
                    Storage::disk('public')->delete($employee->photo_url);
                }
                $validated['photo_url'] = $request->file('photo')->store('employees/photos', 'public');
            }

            $employee->update($validated);

            Swal::success([
                'title' => '¡Actualizado!',
                'text' => 'Empleado actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('employees.show', $employee);
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Actualizar!',
                'text' => 'No es posible actualizar el empleado: '.$e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyEmployee(Employee $employee)
    {
        // Verificar si tiene proyectos activos
        if ($employee->projectAssignments()->where('is_active', true)->exists()) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el empleado porque tiene proyectos activos asignados',
                'icon' => 'error',
            ]);

            return back();
        }

        // Verificar si es manager de algún departamento
        if ($employee->managedDepartments()->exists()) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el empleado porque es manager de uno o más departamentos',
                'icon' => 'error',
            ]);

            return back();
        }

        // Soft delete
        $employee->delete();

        Swal::success([
            'title' => 'Eliminado',
            'text' => 'Empleado eliminado correctamete.',
            'icon' => 'success',
        ]);

        return redirect()->route('employees.all');
    }

    /**
     * Eliminar foto del empleado
     */
    public function deletePhoto(Employee $employee)
    {
        if ($employee->photo_url) {
            Storage::disk('public')->delete($employee->photo_url);
            $employee->update(['photo_url' => null]);

            Swal::success([
                'title' => 'Eliminado',
                'text' => 'Foto eliminada exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('employees.show', $employee);
        }

        Swal::error([
            'title' => 'Error',
            'text' => 'No hay foto para eliminar',
            'icon' => 'error',
        ]);

        return redirect()->route('employees.show', $employee);
    }

    public function exportEmployees()
    {
        Swal::success([
            'title' => 'Exportación exitosa',
            'text' => 'Los empleados se han exportado correctamente.',
            'success' => 'success',
        ]);

        return Excel::download(new EmployeesExport, 'employees.xlsx');
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Por favor selecciona un archivo',
            'file.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
            'file.max' => 'El archivo no debe superar los 5MB',
        ]);

        try {
            Excel::import(new EmployeesImport, $request->file('file'));

            Swal::success([
                'title' => '¡Importación exitosa!',
                'text' => 'Los empleados se han importado correctamente.',
                'icon' => 'success',
                'timer' => 3000,
            ]);

            return redirect()->route('employees.all');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Fila {$failure->row()}: ".implode(', ', $failure->errors());
            }

            Swal::error([
                'title' => 'Error de validación',
                'html' => '<ul class="text-start">'.implode('', array_map(fn ($e) => "<li>$e</li>", array_slice($errors, 0, 10))).'</ul>',
                'icon' => 'error',
            ]);

            return back();
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Error en la importación',
                'text' => 'No se pudo importar el archivo: '.$e->getMessage(),
                'icon' => 'error',
            ]);

            return back();
        }
    }
}
