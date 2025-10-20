<?php

namespace App\Http\Controllers;

use App\Exports\ProjectsExport;
use App\Imports\ProjectsImport;
use App\Models\Project;
use App\Models\Employee;
use App\Models\ProjectAssignment;
use App\Models\ProjectAssignments;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use SweetAlert2\Laravel\Swal;

class ProjectController extends Controller
{
    public function projects(Request $request)
    {
        $query = Project::with(['projectManager', 'projectAssignments']);

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $projects = $query->paginate($request->get('per_page', 10))->withQueryString();

        return Inertia::render('App/Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
            'statuses' => Project::getStatuses(),
        ]);
    }

    public function createProject()
    {
        return Inertia::render('App/Projects/Create', [
            'employees' => Employee::where('status', 'active')
                ->select('id', 'first_name', 'last_name', 'email')
                ->orderBy('first_name')
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->first_name . ' ' . $e->last_name,
                    'email' => $e->email,
                ]),
            'suggestedCode' => Project::generateCode(),
            'statuses' => Project::getStatuses(),
        ]);
    }

    public function storeProject(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
                'description' => ['nullable', 'string'],
                'start_date' => ['required', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'status' => ['required', 'in:' . implode(',', array_keys(Project::getStatuses()))],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'project_manager_id' => ['nullable', 'exists:employees,id'],
            ]);

            Project::create($validated);

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Proyecto creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('projects.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo crear el proyecto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    public function showProject(Project $project)
    {
        $project->load([
            'projectManager',
            'projectAssignments' => function ($q) {
                $q->with(['employee.position', 'employee.department'])
                    ->orderBy('is_active', 'desc')
                    ->orderBy('assigned_date', 'desc');
            }
        ]);

        $stats = [
            'total_employees' => $project->projectAssignments->count(),
            'active_employees' => $project->projectAssignments->where('is_active', true)->count(),
            'days_remaining' => $project->days_remaining,
            'progress_percentage' => $project->progress_percentage,
        ];

        return Inertia::render('App/Projects/Show', [
            'project' => $project,
            'stats' => $stats,
            'availableEmployees' => Employee::where('status', 'active')
                ->whereDoesntHave('projectAssignments', function ($q) use ($project) {
                    $q->where('project_id', $project->id)->where('is_active', true);
                })
                ->select('id', 'first_name', 'last_name', 'email')
                ->orderBy('first_name')
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->first_name . ' ' . $e->last_name,
                    'email' => $e->email,
                ]),
        ]);
    }

    public function editProject(Project $project)
    {
        $project = Project::with(['projectManager', 'projectAssignments'])
            ->findOrFail($project->id);

        $employees = Employee::where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => "{$employee->first_name} {$employee->last_name}",
                    'email' => $employee->email,
                ];
            });

        $statuses = [
            'planning' => 'Planificación',
            'active' => 'Activo',
            'on_hold' => 'En Espera',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
        ];

        return Inertia::render('App/Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'code' => $project->code,
                'description' => $project->description,
                'start_date' => $project->start_date ? date('Y-m-d', strtotime($project->start_date)) : null,
                'end_date' => $project->end_date ? date('Y-m-d', strtotime($project->end_date)) : null,
                'status' => $project->status,
                'budget' => $project->budget,
                'project_manager_id' => $project->project_manager_id,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
                'project_assignments' => $project->project_assignments,
            ],
            'employees' => $employees,
            'statuses' => $statuses,
        ]);
    }

    public function updateProject(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:50', 'unique:projects,code,' . $project->id],
                'description' => ['nullable', 'string'],
                'start_date' => ['required', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'status' => ['required', 'in:' . implode(',', array_keys(Project::getStatuses()))],
                'budget' => ['nullable', 'numeric', 'min:0'],
                'project_manager_id' => ['nullable', 'exists:employees,id'],
            ]);

            $project->update($validated);

            Swal::success([
                'title' => '¡Actualizado!',
                'text' => 'Proyecto actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('projects.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo actualizar el proyecto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    public function destroyProject(Project $project)
    {
        try {
            if ($project->projectAssignments()->where('is_active', true)->exists()) {
                Swal::error([
                    'title' => 'No se puede eliminar',
                    'text' => 'El proyecto tiene empleados activos asignados',
                    'icon' => 'warning',
                ]);
                return back();
            }

            $project->delete();

            Swal::success([
                'title' => 'Eliminado',
                'text' => 'Proyecto eliminado correctamente',
                'icon' => 'success',
            ]);

            return redirect()->route('projects.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo eliminar el proyecto: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back();
        }
    }

    public function assignEmployee(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'employee_id' => ['required', 'exists:employees,id'],
                'role' => ['nullable', 'string', 'max:255'],
                'assigned_date' => ['required', 'date'],
                'allocation_percentage' => ['required', 'integer', 'min:1', 'max:100'],
            ]);

            // Verificar que el empleado no esté ya asignado activamente
            $exists = ProjectAssignments::where('project_id', $project->id)
                ->where('employee_id', $validated['employee_id'])
                ->where('is_active', true)
                ->exists();

            if ($exists) {
                Swal::warning([
                    'title' => 'Ya asignado',
                    'text' => 'El empleado ya está asignado activamente a este proyecto',
                    'icon' => 'warning',
                ]);
                return back();
            }

            ProjectAssignments::create([
                'project_id' => $project->id,
                'employee_id' => $validated['employee_id'],
                'role' => $validated['role'],
                'assigned_date' => $validated['assigned_date'],
                'allocation_percentage' => $validated['allocation_percentage'],
                'is_active' => true,
            ]);

            Swal::success([
                'title' => 'Asignado',
                'text' => 'El empleado ha sido asignado al proyecto con exito',
                'icon' => 'success'
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo asignar el empleado: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back();
        }
    }

    public function removeEmployee(Project $project, ProjectAssignments $assignment)
    {
        try {
            if ($assignment->project_id !== $project->id) {
                abort(403, 'No autorizado');
            }

            $assignment->update([
                'is_active' => false,
                'end_date' => now(),
            ]);

            Swal::success([
                'title' => 'Asignado',
                'text' => 'El empleado ha sido removido del proyecto con exito',
                'icon' => 'success'
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo remover el empleado: ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back();
        }
    }

    public function export()
    {
        Swal::success([
            'title' => 'Exportación completada',
            'text' => 'El archivo de proyectos se generó correctamente.',
            'icon' => 'success'
        ]);
        return Excel::download(new ProjectsExport, 'projects.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Please select a file.',
            'file.mimes' => 'The file must be an Excel (.xlsx, .xls) or CSV file.',
            'file.max' => 'The file may not exceed 5MB.',
        ]);

        try {
            Excel::import(new ProjectsImport, $request->file('file'));

            Swal::success([
                'title' => 'Importacion',
                'text' => 'Los Proyectos han sido importados correctamente.',
                'icon' => 'success',
                'timer' => 3000,
            ]);

            return redirect()->route('projects.all');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = collect($e->failures())->map(fn($f) => "Row {$f->row()}: " . implode(', ', $f->errors()))->take(10);

            Swal::error([
                'title' => 'Validation Error',
                'html' => '<ul class="text-start">' . $errors->map(fn($e) => "<li>$e</li>")->implode('') . '</ul>',
                'icon' => 'error',
            ]);

            return back();
        } catch (\Exception $e) {
            Swal::error([
                'title' => 'Import Error',
                'text' => 'Could not import file: ' . $e->getMessage(),
                'icon' => 'error',
            ]);

            return back();
        }
    }
}
