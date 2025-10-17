<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function positions(Request $request)
    {
        $query = Position::with('employees');

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filtro por estado
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtro por nivel
        if ($request->has('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

        // Ordenamiento
        $sortField = $request->get('sort_field', 'title');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $positions = $query->paginate($request->get('per_page', 10))
            ->withQueryString();

        return Inertia::render('App/Positions/Index', [
            'positions' => $positions,
            'filters' => $request->only(['search', 'status', 'level', 'sort_field', 'sort_direction', 'per_page']),
            'levels' => Position::getLevels(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createPosition()
    {
        return Inertia::render('App/Positions/Create', [
            'levels' => Position::getLevels(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:junior,mid,senior,lead,manager,director'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'min:0', 'gte:min_salary'],
            'is_active' => ['boolean'],
        ], [
            'title.required' => 'El título del puesto es obligatorio',
            'level.required' => 'El nivel es obligatorio',
            'level.in' => 'El nivel seleccionado no es válido',
            'min_salary.numeric' => 'El salario mínimo debe ser un número válido',
            'min_salary.min' => 'El salario mínimo no puede ser negativo',
            'max_salary.numeric' => 'El salario máximo debe ser un número válido',
            'max_salary.min' => 'El salario máximo no puede ser negativo',
            'max_salary.gte' => 'El salario máximo debe ser mayor o igual al mínimo',
        ]);

        $position = Position::create($validated);

        return redirect()->route('positions.all')
            ->with('success', 'Puesto creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function showPositionDetails($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return back()->with('error', 'No se encontro el Puesto con id: ' . $id);
        }

        $position->load([
            'employees' => function ($query) {
                $query->with('department')
                    ->orderBy('first_name');
            }
        ]);

        // Estadísticas
        $stats = [
            'total_employees' => $position->employees->count(),
            'active_employees' => $position->employees->where('status', 'active')->count(),
            'average_salary' => $position->employees->avg('salary'),
            'min_employee_salary' => $position->employees->min('salary'),
            'max_employee_salary' => $position->employees->max('salary'),
            'departments_distribution' => $position->employees
                ->groupBy('department.name')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return Inertia::render('App/Positions/Show', [
            'position' => $position,
            'stats' => $stats,
            'levels' => Position::getLevels(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editPosition($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return back()->with('error', 'No se encontro Puesto con el id: ' . $id);
        }

        return Inertia::render('App/Positions/Edit', [
            'position' => $position,
            'levels' => Position::getLevels(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePosition(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:junior,mid,senior,lead,manager,director'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'min:0', 'gte:min_salary'],
            'is_active' => ['boolean'],
        ], [
            'title.required' => 'El título del puesto es obligatorio',
            'level.required' => 'El nivel es obligatorio',
            'level.in' => 'El nivel seleccionado no es válido',
            'min_salary.numeric' => 'El salario mínimo debe ser un número válido',
            'min_salary.min' => 'El salario mínimo no puede ser negativo',
            'max_salary.numeric' => 'El salario máximo debe ser un número válido',
            'max_salary.min' => 'El salario máximo no puede ser negativo',
            'max_salary.gte' => 'El salario máximo debe ser mayor o igual al mínimo',
        ]);

        $position = Position::find($id);

        if (!$position) {
            return back()->with('error', 'No se encontro Puesto con el id: ' . $id);
        }

        $position->update($validated);

        return redirect()->route('positions.all')
            ->with('success', 'Puesto actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyPosition($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return back()->with('error', 'No se encontro Puesto con el id:', $id);
        }

        if (!$position->canBeDeleted()) {
            return back()->with('error', 'No se puede eliminar el puesto porque tiene empleados asignados');
        }

        $position->delete();

        return redirect()->route('positions.all')
            ->with('success', 'Puesto eliminado exitosamente');
    }
}
