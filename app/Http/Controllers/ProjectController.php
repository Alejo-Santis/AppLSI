<?php

namespace App\Http\Controllers;

use App\Exports\ProjectsExport;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Imports\ProjectsImport;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectAssignments;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->first_name.' '.$e->last_name,
                    'email' => $e->email,
                ]),
            'suggestedCode' => Project::generateCode(),
            'statuses' => Project::getStatuses(),
        ]);
    }

    public function storeProject(StoreProjectRequest $request)
    {
        try {
            Project::create($request->validated());

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Proyecto creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('projects.all');
        } catch (Exception $e) {
            Log::error('Error al crear proyecto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo crear el proyecto: '.$e->getMessage(),
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
            },
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
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->first_name.' '.$e->last_name,
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

    public function updateProject(UpdateProjectRequest $request, Project $project)
    {
        try {
            if (! $project->exists()) {
                Swal::error([
                    'title' => 'Error',
                    'text' => 'Proyecto no encontrado',
                    'icon' => 'error',
                ]);

                return back();
            }

            $project->update($request->validated());

            Swal::success([
                'title' => '¡Actualizado!',
                'text' => 'Proyecto actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('projects.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo actualizar el proyecto: '.$e->getMessage(),
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
                'text' => 'No se pudo eliminar el proyecto: '.$e->getMessage(),
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
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo asignar el empleado: '.$e->getMessage(),
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
                'icon' => 'success',
            ]);

            return back();
        } catch (Exception $e) {
            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudo remover el empleado: '.$e->getMessage(),
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
            'icon' => 'success',
        ]);

        return Excel::download(new ProjectsExport, 'projects.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Por favor selecciona un archivo',
            'file.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV',
            'file.max' => 'El archivo no debe superar los 5MB',
        ]);

        try {
            Excel::import(new ProjectsImport, $request->file('file'));

            Swal::success([
                'title' => '¡Importación exitosa!',
                'text' => 'Los proyectos se han importado correctamente.',
                'icon' => 'success',
                'timer' => 5000,
            ]);

            return back();
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
