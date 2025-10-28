<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use SweetAlert2\Laravel\Swal;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            // Información de autenticación mejorada
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role, // Rol básico (admin, hr, manager, employee)
                    'is_active' => $request->user()->is_active,
                    'last_login' => $request->user()->last_login,
                    'avatar' => $request->user()->avatar,

                    // Roles de Spatie (array de nombres)
                    'roles' => $request->user()->roles->pluck('name')->toArray(),

                    // Permisos (array de nombres de permisos)
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),

                    // Métodos helper booleanos
                    'is_admin' => $request->user()->hasRole('Admin'),
                    'is_hr' => $request->user()->hasRole('HR'),
                    'is_manager' => $request->user()->hasRole('Manager'),
                    'is_employee' => $request->user()->hasRole('Employee'),
                ] : null,
            ],

            'flash' => [
                'data' => fn () => $request->session()->get(Swal::SESSION_KEY),
            ],
        ];
    }
}
