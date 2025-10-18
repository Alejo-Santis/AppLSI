<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SweetAlert2\Laravel\Swal;
use Exception;

class PositionController extends Controller
{
    public function positions(Request $request)
    {
        $query = Position::with('employees');

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

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

    public function createPosition()
    {
        return Inertia::render('App/Positions/Create', [
            'levels' => Position::getLevels(),
        ]);
    }

    public function storePosition(Request $request)
    {
        try {
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

            Position::create($validated);

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Puesto creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('positions.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se pudo crear el puesto. ' . $e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    public function showPositionDetails($id)
    {
        $position = Position::find($id);

        if (!$position) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se encontró el puesto con el ID proporcionado.',
                'icon' => 'error',
            ]);
            return back();
        }

        $position->load([
            'employees' => function ($query) {
                $query->with('department')
                    ->orderBy('first_name');
            }
        ]);

        $stats = [
            'total_employees' => $position->employees->count(),
            'active_employees' => $position->employees->where('status', 'active')->count(),
            'average_salary' => $position->employees->avg('salary'),
            'min_employee_salary' => $position->employees->min('salary'),
            'max_employee_salary' => $position->employees->max('salary'),
            'departments_distribution' => $position->employees
                ->groupBy('department.name')
                ->map(fn($group) => $group->count()),
        ];

        return Inertia::render('App/Positions/Show', [
            'position' => $position,
            'stats' => $stats,
            'levels' => Position::getLevels(),
        ]);
    }

    public function editPosition($id)
    {
        $position = Position::find($id);

        if (!$position) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se encontró el puesto para editar.',
                'icon' => 'error',
            ]);
            return back();
        }

        return Inertia::render('App/Positions/Edit', [
            'position' => $position,
            'levels' => Position::getLevels(),
        ]);
    }

    public function updatePosition(Request $request, $id)
    {
        try {
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
                Swal::error([
                    'title' => '¡Error!',
                    'text' => 'No se encontró el puesto con ese ID.',
                    'icon' => 'error',
                ]);
                return back();
            }

            $position->update($validated);

            Swal::success([
                'title' => 'Actualizado!',
                'text' => 'Puesto actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('positions.all');
        } catch (Exception $e) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se pudo actualizar el puesto. ' . $e->getMessage(),
                'icon' => 'error',
            ]);
            return back()->withInput();
        }
    }

    public function destroyPosition($id)
    {
        $position = Position::find($id);

        if (!$position) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se encontró el puesto con ese ID.',
                'icon' => 'error',
            ]);
            return back();
        }

        if (!$position->canBeDeleted()) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se puede eliminar el puesto porque tiene empleados asignados.',
                'icon' => 'error',
            ]);
            return back();
        }

        $position->delete();

        Swal::success([
            'title' => '¡Eliminado!',
            'text' => 'Puesto eliminado exitosamente',
            'icon' => 'success',
        ]);

        return redirect()->route('positions.all');
    }
}
