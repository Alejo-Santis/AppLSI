<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department', 'position')->get();

        // Datos resumen
        $stats = [
            'total_employees'     => $employees->count(),
            'active_employees'    => $employees->where('status', 'active')->count(),
            'total_departments'   => Department::count(),
            'total_positions'     => Position::count(),
            'avg_years_service'   => round($employees->avg(fn($e) => $e->hire_date ? $e->hire_date->diffInYears(Carbon::now()) : 0), 1),
        ];

        // Empleados por departamento (para gráfico)
        $employeesByDept = Department::withCount('employees')
            ->get()
            ->map(fn($d) => [
                'name'  => $d->name,
                'count' => $d->employees_count,
            ]);

        // Empleados por nivel de puesto
        $employeesByLevel = Position::withCount('employees')
            ->get()
            ->groupBy('level')
            ->map(fn($group, $level) => [
                'level' => ucfirst($level ?: 'Sin nivel'),
                'count' => $group->sum('employees_count'),
            ])
            ->values();

        return Inertia::render('App/Dashboard', [
            'stats' => $stats,
            'employeesByDept' => $employeesByDept,
            'employeesByLevel' => $employeesByLevel,
        ]);
    }
}
