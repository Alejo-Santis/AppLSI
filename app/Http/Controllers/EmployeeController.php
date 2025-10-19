<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
    public function storeEmployee(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:employees,email'],
                'phone' => ['nullable', 'string', 'max:20'],
                'hire_date' => ['required', 'date'],
                'salary' => ['required', 'numeric', 'min:0'],
                'status' => ['required', 'in:active,inactive,on_leave,terminated'],
                'birth_date' => ['nullable', 'date', 'before:today'],
                'address' => ['nullable', 'string'],
                'photo' => ['nullable', 'image', 'max:2048'], // 2MB max
                'position_id' => ['required', 'exists:positions,id'],
                'department_id' => ['required', 'exists:departments,id'],
            ], [
                'first_name.required' => 'El nombre es obligatorio',
                'last_name.required' => 'El apellido es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'Ingrese un email válido',
                'email.unique' => 'Este email ya está registrado',
                'hire_date.required' => 'La fecha de contratación es obligatoria',
                'salary.required' => 'El salario es obligatorio',
                'salary.min' => 'El salario no puede ser negativo',
                'position_id.required' => 'Debe seleccionar un puesto',
                'position_id.exists' => 'El puesto seleccionado no existe',
                'department_id.required' => 'Debe seleccionar un departamento',
                'department_id.exists' => 'El departamento seleccionado no existe',
                'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy',
                'photo.image' => 'El archivo debe ser una imagen',
                'photo.max' => 'La imagen no puede superar los 2MB',
            ]);

            // Procesar foto si existe
            if ($request->hasFile('photo')) {
                $validated['photo_url'] = $request->file('photo')->store('employees/photos', 'public');
            }

            $employee = Employee::create($validated);

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Empleado creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('employees.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Crear!',
                'text' => 'No es posible crear el empleado' . $e->getMessage(),
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
            'documents' => fn($q) => $q->orderBy('created_at', 'desc'),
            'emergencyContacts',
            'salaryHistories' => fn($q) => $q->orderBy('change_date', 'desc'),
            'projectAssignments.project',
        ]);

        // Evitar errores si hire_date es nulo
        if (!$employee->hire_date) {
            $stats = [
                'years_of_service'  => 0,
                'months_in_company' => 0,
                'total_documents'   => $employee->documents->count(),
                'active_projects'   => $employee->projectAssignments->where('is_active', true)->count(),
                'salary_changes'    => $employee->salaryHistories->count(),
            ];
        } else {
            $hireDate = Carbon::parse($employee->hire_date)->startOfDay();
            $now = now()->startOfDay();

            // Usamos diff() para obtener años y meses exactos, no fraccionados
            $diff = $hireDate->diff($now);

            $stats = [
                'years_of_service'  => (int) $diff->y,
                'months_in_company' => (int) ($diff->y * 12 + $diff->m),
                'total_documents'   => $employee->documents->count(),
                'active_projects'   => $employee->projectAssignments->where('is_active', true)->count(),
                'salary_changes'    => $employee->salaryHistories->count(),
            ];
        }

        return Inertia::render('App/Employees/Show', [
            'employee' => $employee,
            'stats'    => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editEmployee(Employee $employee)
    {
        return Inertia::render('App/Employees/Edit', [
            'employee' => [
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
                'department_id' => $employee->department_ids,
                $employee
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
    public function updateEmployee(Request $request, Employee $employee)
    {
        try {

            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:employees,email,' . $employee->id],
                'phone' => ['nullable', 'string', 'max:20'],
                'hire_date' => ['required', 'date'],
                'salary' => ['required', 'numeric', 'min:0'],
                'status' => ['required', 'in:active,inactive,on_leave,terminated'],
                'birth_date' => ['nullable', 'date', 'before:today'],
                'address' => ['nullable', 'string'],
                'photo' => ['nullable', 'image', 'max:2048'],
                'position_id' => ['required', 'exists:positions,id'],
                'department_id' => ['required', 'exists:departments,id'],
            ], [
                'first_name.required' => 'El nombre es obligatorio',
                'last_name.required' => 'El apellido es obligatorio',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'Ingrese un email válido',
                'email.unique' => 'Este email ya está registrado',
                'hire_date.required' => 'La fecha de contratación es obligatoria',
                'salary.required' => 'El salario es obligatorio',
                'salary.min' => 'El salario no puede ser negativo',
                'position_id.required' => 'Debe seleccionar un puesto',
                'department_id.required' => 'Debe seleccionar un departamento',
                'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            ]);

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
                'title' => '¡Acualizado!',
                'text' => 'Empleado acualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('employees.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error al Actualizar!',
                'text' => 'No es posible acualizar el empleado' . $e->getMessage(),
                'icon' => 'success',
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
            'icon' => 'success'
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
        }
        Swal::success([
            'title' => 'Eliminado',
            'text' => 'Foto eliminada exitosamente',
            'icon' => 'success',
        ]);

        return back();
    }
}
