<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use SweetAlert2\Laravel\Swal;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with(['permissions']);

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
        }

        $roles = $query->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->paginate($request->get('per_page', 10))
            ->withQueryString();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        $permissions = Permission::select('id', 'name', 'description', 'module')
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
                'description' => ['nullable', 'string', 'max:500'],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['exists:permissions,id'],
            ], [
                'name.required' => 'El nombre del rol es obligatorio.',
                'name.unique' => 'Ya existe un rol con ese nombre.',
                'permissions.*.exists' => 'Uno o más permisos seleccionados no existen.',
            ]);

            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'guard_name' => 'web',
            ]);

            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            Swal::success([
                'title' => 'Operación Exitosa',
                'text' => 'Departamento Creado Exitosamente.',
                'icon' => 'success',
            ]);

            return redirect()->route('admin.roles.index');

        } catch (Exception $e) {
            Log::error('Error al crear rol', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Swal::error([
                'title' => 'Error al crear rol',
                'text' => 'Ocurrió un error inesperado. Por favor, intenta nuevamente.',
                'icon' => 'error',
            ]);

            return back()->withInput();
        }
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        $permissions = Permission::select('id', 'name', 'description', 'module')
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        try {
            // Prevenir edición de roles del sistema
            if (in_array($role->name, ['Admin', 'HR', 'Manager', 'Employee'])) {
                Swal::warning([
                    'title' => 'Acción no permitida',
                    'text' => 'No se pueden editar los roles del sistema. Puedes crear un rol personalizado.',
                    'icon' => 'warning',
                ]);

                return back();
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
                'description' => ['nullable', 'string', 'max:500'],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['exists:permissions,id'],
            ]);

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            } else {
                $role->syncPermissions([]);
            }

            // Limpiar caché de permisos
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            Swal::success([
                'title' => 'Operación Exitosa',
                'text' => 'Departamento Creado Exitosamente.',
                'icon' => 'success',
            ]);

            return redirect()
                ->route('admin.roles.index');

        } catch (Exception $e) {
            Log::error('Error al actualizar rol', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);

            Swal::error([
                'title' => 'Error al actualizar',
                'text' => 'Ocurrió un error inesperado.',
                'icon' => 'error',
            ]);

            return back()
                ->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            // Prevenir eliminación de roles del sistema
            if (in_array($role->name, ['Admin', 'HR', 'Manager', 'Employee'])) {
                return back()->with('error', [
                    'title' => 'No se puede eliminar',
                    'message' => 'Los roles del sistema no pueden ser eliminados.',
                ]);
            }

            // Verificar si tiene usuarios asignados
            $usersCount = $role->users()->count();
            if ($usersCount > 0) {
                Swal::warning([
                    'title' => 'No se puede eliminar',
                    'text' => "El rol tiene {$usersCount} usuario(s) asignado(s). Primero remuévelos del rol.",
                    'icon' => 'warning',
                ]);

                return back();
            }

            $roleName = $role->name;
            $role->delete();

            Swal::success([
                'title' => '¡Eliminado!',
                'text' => "El rol '{$roleName}' ha sido eliminado correctamente.",
                'icon' => 'success',
            ]);

            return redirect()
                ->route('admin.roles.index');

        } catch (Exception $e) {
            Log::error('Error al eliminar rol', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);

            Swal::error([
                'title' => 'Error al eliminar',
                'text' => 'Ocurrió un error inesperado.',
                'icon' => 'error',
            ]);

            return back();
        }
    }

    public function permissions(Role $role)
    {
        $role->load('permissions');

        $allPermissions = Permission::select('id', 'name', 'description', 'module')
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Admin/Roles/Permissions', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function syncPermissions(Request $request, Role $role)
    {
        try {
            $validated = $request->validate([
                'permissions' => ['required', 'array'],
                'permissions.*' => ['exists:permissions,id'],
            ]);

            $role->syncPermissions($validated['permissions']);

            // Limpiar caché
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            Swal::success([
                'title' => '¡Actualizado!',
                'text' => "Los permisos del rol '{$role->name}' han sido actualizados.",
                'icon' => 'success',
            ]);

            return redirect()->route('admin.roles.permissions', $role);

        } catch (Exception $e) {
            Log::error('Error al actualizar permisos del rol', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);

            Swal::error([
                'title' => 'Error',
                'text' => 'No se pudieron actualizar los permisos.',
                'icon' => 'error',
            ]);

            return back();
        }
    }
}
