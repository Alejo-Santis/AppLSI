<?php

namespace App\Http\Controllers;

use App\Exports\DepartmentsExport;
use App\Imports\DepartmentsImport;
use App\Models\Department;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use SweetAlert2\Laravel\Swal;

class DepartmentController extends Controller
{
    public function departments(Request $request)
    {
        $query = Department::with(['manager', 'employees']);

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status == 'active');
        }

        if ($request->has('has_manager') && $request->has_manager !== 'all') {
            if ($request->has_manager === 'with') {
                $query->whereNotNull('manager_id');
            } else {
                $query->whereNull('manager_id');
            }
        }

        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $departments = $query->paginate($request->get('per_page', 10))
            ->withQueryString();

        return Inertia::render('App/Departments/Index', [
            'departments' => $departments,
            'filters' => $request->only(['search', 'status', 'has_manager', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDepartment()
    {
        $employees = Employee::where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $employee->email,
                ];
            });

        return Inertia::render('App/Departments/Create', [
            'employees' => $employees,
            'suggestedCode' => Department::generateCode(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeDepartment(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:50', 'unique:departments,code'],
                'description' => ['nullable', 'string'],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'manager_id' => ['nullable', 'exists:employees,id'],
                'is_active' => ['boolean'],
            ], [
                'name.required' => 'El nombre del departamento es obligatorio',
                'code.required' => 'El código es obligatorio',
                'code.unique' => 'Este código ya está en uso',
                'budget.numeric' => 'El presupuesto debe ser un número válido',
                'budget.min' => 'El presupuesto no puede ser negativo',
                'manager_id.exists' => 'El empleado seleccionado no existe',
            ]);

            $department = Department::create($validated);

            Swal::success([
                'title' => 'Operación Exitosa',
                'text' => 'Departamento Creado Exitosamente.',
            ]);

            return redirect()->route('departments.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'Ocurrió un error al crear el departamento.' . $e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function showDepartmentDetails($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return back()->with('error', 'No se encontro el departamento.');
        }

        $department->load([
            'manager',
            'employees' => function ($query) {
                $query->with('position')
                    ->orderBy('first_name');
            }
        ]);

        // Estadísticas
        $stats = [
            'total_employees' => $department->employees->count(),
            'active_employees' => $department->employees->where('status', 'active')->count(),
            'total_salary' => $department->employees->sum('salary'),
            'average_salary' => $department->employees->avg('salary'),
            'positions_distribution' => $department->employees
                ->groupBy('position.title')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return Inertia::render('App/Departments/Show', [
            'department' => $department,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editDepartment($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return back()->with('error', 'No se encontro el departamento a editar.');
        }

        $employees = Employee::where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'email' => $employee->email,
                ];
            });

        return Inertia::render('App/Departments/Edit', [
            'department' => $department,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDeparment(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('departments', 'code')->ignore($id),
                ],
                'description' => ['nullable', 'string'],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'manager_id' => ['nullable', 'exists:employees,id'],
                'is_active' => ['boolean'],
            ], [
                'name.required' => 'El nombre del departamento es obligatorio',
                'code.required' => 'El código es obligatorio',
                'code.unique' => 'Este código ya está en uso',
                'budget.numeric' => 'El presupuesto debe ser un número válido',
                'budget.min' => 'El presupuesto no puede ser negativo',
                'manager_id.exists' => 'El empleado seleccionado no existe',
            ]);

            $department = Department::find($id);

            if (!$department) {
                Swal::error([
                    'title' => '¡Error!',
                    'text' => 'No se encontro el departamento con ese id',
                    'icon' => 'error',
                ]);
                return back();
            }

            $department->update($validated);

            Swal::success([
                'title' => 'Actualizado!',
                'text' => 'Departamento actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('departments.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede actualizar el departamento.' . $e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyDepartment($id)
    {
        $department = Department::find($id);

        if (!$department) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el departamento porque tiene empleados asignados',
                'icon' => 'error',
            ]);

            return back();
        }

        if (!$department->canBeDeleted()) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el departamento porque tiene empleados asignados',
                'icon' => 'error',
            ]);
            return back();
        }

        $department->delete();

        Swal::success([
            'title' => '¡Eliminado!',
            'text' => 'Departamento eliminado exitosamente',
            'icon' => 'success',
        ]);

        return redirect()->route('departments.all');
    }

    /**
     * Obtener empleados del departamento (API endpoint)
     */
    public function employees(Department $department)
    {
        $employees = $department->employees()
            ->with('position')
            ->where('status', 'active')
            ->get();

        return response()->json($employees);
    }

    public function exportDepartments()
    {
        Swal::success([
            'title' => 'Exportación exitosa',
            'text' => 'Los departamentos se han exportado correctamente.',
            'success' => 'success'
        ]);

        return Excel::download(new DepartmentsExport, 'departamentos.xlsx');
    }

    public function importDepartments(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // 5MB max
        ], [
            'file.required' => 'Por favor selecciona un archivo',
            'file.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
            'file.max' => 'El archivo no debe superar los 5MB',
        ]);

        try {
            Excel::import(new DepartmentsImport, $request->file('file'));

            Swal::success([
                'title' => '¡Importación exitosa!',
                'text' => 'Los departamentos se han importado correctamente.',
                'icon' => 'success',
                'timer' => 3000,
            ]);

            return redirect()->route('departments.all');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
            }

            Swal::error([
                'title' => 'Error de validación',
                'html' => '<ul class="text-start">' . implode('', array_map(fn($e) => "<li>$e</li>", $errors)) . '</ul>',
                'icon' => 'error',
            ]);

            return back();
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Error en la importación',
                'text' => 'No se pudo importar el archivo: ' . $e->getMessage(),
                'icon' => 'error',
            ]);

            return back();
        }
    }
}
