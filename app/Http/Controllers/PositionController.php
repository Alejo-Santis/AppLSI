<?php

namespace App\Http\Controllers;

use App\Exports\PositionsExport;
use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Imports\PositionsImport;
use App\Models\Position;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use SweetAlert2\Laravel\Swal;

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

    public function storePosition(StorePositionRequest $request)
    {
        try {
            $position = Position::create($request->validated());

            Swal::success([
                'title' => '¡Creado!',
                'text' => 'Puesto creado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('positions.all');
        } catch (Exception $e) {
            Log::error('Error al crear puesto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->validated(),
            ]);

            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se pudo crear el puesto. '.$e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    public function showPositionDetails($id)
    {
        $position = Position::find($id);

        if (! $position) {
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
            },
        ]);

        $stats = [
            'total_employees' => $position->employees->count(),
            'active_employees' => $position->employees->where('status', 'active')->count(),
            'average_salary' => $position->employees->avg('salary'),
            'min_employee_salary' => $position->employees->min('salary'),
            'max_employee_salary' => $position->employees->max('salary'),
            'departments_distribution' => $position->employees
                ->groupBy('department.name')
                ->map(fn ($group) => $group->count()),
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

        if (! $position) {
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

    public function updatePosition(UpdatePositionRequest $request, $id)
    {
        try {
            $position = Position::find($id);

            if (!$position) {
                Swal::error([
                    'title' => '¡Error!',
                    'text' => 'No se encontró el puesto con ese ID.',
                    'icon' => 'error',
                ]);

                return back();
            }

            $position->update($request->validated());

            Swal::success([
                'title' => 'Actualizado!',
                'text' => 'Puesto actualizado exitosamente',
                'icon' => 'success',
            ]);

            return redirect()->route('positions.all');
        } catch (Exception $e) {
            Log::error('Error al actualizar puesto', [
                'position_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se pudo actualizar el puesto. '.$e->getMessage(),
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    public function destroyPosition($id)
    {
        $position = Position::find($id);

        if (! $position) {
            Swal::error([
                'title' => '¡Error!',
                'text' => 'No se encontró el puesto con ese ID.',
                'icon' => 'error',
            ]);

            return back();
        }

        if (! $position->canBeDeleted()) {
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

    /**
     * Exportar positions.
     */
    public function export()
    {
        Swal::success([
            'title' => 'Exportación exitosa',
            'text' => 'Los puestos se han exportado correctamente.',
            'success' => 'success',
        ]);

        return Excel::download(new PositionsExport, 'positions.xlsx');
    }

    /**
     * Importar positions.
     */
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
            Excel::import(new PositionsImport, $request->file('file'));

            Swal::success([
                'title' => '¡Importación exitosa!',
                'text' => 'Los puestos se han importado correctamente.',
                'icon' => 'success',
                'timer' => 3000,
            ]);

            return redirect()->route('positions.all');
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
