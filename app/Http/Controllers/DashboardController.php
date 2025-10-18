<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Project;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas principales
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'inactive_employees' => Employee::where('status', 'inactive')->count(),
            'on_leave_employees' => Employee::where('status', 'on_leave')->count(),
            'total_departments' => Department::count(),
            'active_departments' => Department::where('is_active', true)->count(),
            'total_positions' => Position::count(),
            'active_positions' => Position::where('is_active', true)->count(),
            'total_projects' => Project::count() ?? 0,
            'active_projects' => Project::where('status', 'active')->count() ?? 0,
        ];

        // Estadísticas financieras
        $financialStats = [
            'total_payroll' => Employee::where('status', 'active')->sum('salary'),
            'avg_salary' => Employee::where('status', 'active')->avg('salary'),
            'min_salary' => Employee::where('status', 'active')->min('salary'),
            'max_salary' => Employee::where('status', 'active')->max('salary'),
        ];

        // Empleados por departamento (para gráfico de barras)
        $employeesByDept = Department::withCount([
            'employees' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->orderBy('employees_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($d) => [
                'name' => $d->name,
                'count' => $d->employees_count,
                'code' => $d->code,
            ]);

        // Empleados por nivel de puesto (para gráfico de dona)
        $employeesByLevel = Position::withCount([
            'employees' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->get()
            ->groupBy('level')
            ->map(fn($group, $level) => [
                'level' => $this->getLevelLabel($level),
                'count' => $group->sum('employees_count'),
            ])
            ->values()
            ->sortByDesc('count')
            ->values();

        // Timeline de contrataciones (últimos 12 meses)
        // Detectar driver de base de datos
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL
            $hiringTimeline = Employee::select(
                DB::raw("TO_CHAR(hire_date, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
                ->where('hire_date', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
                ->map(fn($item) => [
                    'month' => Carbon::parse($item->month . '-01')->format('M Y'),
                    'count' => $item->count,
                ]);
        } else {
            // MySQL
            $hiringTimeline = Employee::select(
                DB::raw('DATE_FORMAT(hire_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
                ->where('hire_date', '>=', Carbon::now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
                ->map(fn($item) => [
                    'month' => Carbon::parse($item->month . '-01')->format('M Y'),
                    'count' => $item->count,
                ]);
        }

        // Distribución por estado
        $employeesByStatus = [
            ['status' => 'Activos', 'count' => $stats['active_employees']],
            ['status' => 'Inactivos', 'count' => $stats['inactive_employees']],
            ['status' => 'En Licencia', 'count' => $stats['on_leave_employees']],
        ];

        // Últimos empleados contratados (actividad reciente)
        $recentHires = Employee::with(['department', 'position'])
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'full_name' => $e->full_name,
                'email' => $e->email,
                'position' => $e->position?->title,
                'department' => $e->department?->name,
                'hire_date' => $e->hire_date->format('Y-m-d'),
                'hire_date_human' => $e->hire_date->diffForHumans(),
                'status' => $e->status,
            ]);

        // Departamentos con más empleados (top 5)
        $topDepartments = Department::withCount([
            'employees' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->orderBy('employees_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'code' => $d->code,
                'employees_count' => $d->employees_count,
                'manager' => $d->manager ? $d->manager->full_name : 'Sin asignar',
            ]);

        // Puestos más comunes
        $topPositions = Position::withCount([
            'employees' => function ($query) {
                $query->where('status', 'active');
            }
        ])
            ->orderBy('employees_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'level' => $this->getLevelLabel($p->level),
                'employees_count' => $p->employees_count,
            ]);

        return Inertia::render('App/Dashboard', [
            'stats' => $stats,
            'financialStats' => $financialStats,
            'employeesByDept' => $employeesByDept,
            'employeesByLevel' => $employeesByLevel,
            'employeesByStatus' => $employeesByStatus,
            'hiringTimeline' => $hiringTimeline,
            'recentHires' => $recentHires,
            'topDepartments' => $topDepartments,
            'topPositions' => $topPositions,
        ]);
    }

    /**
     * Obtener etiqueta legible del nivel
     */
    private function getLevelLabel($level)
    {
        $labels = [
            'junior' => 'Junior',
            'mid' => 'Mid-Level',
            'senior' => 'Senior',
            'lead' => 'Lead',
            'manager' => 'Manager',
            'director' => 'Director',
        ];

        return $labels[$level] ?? ucfirst($level);
    }
}
