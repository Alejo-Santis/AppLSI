<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Mapa de roles básicos a roles de Spatie
     */
    private function getRoleMapping(): array
    {
        return [
            'admin' => 'admin',
            'hr' => 'hr',
            'manager' => 'manager',
            'employee' => 'employee',
        ];
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Sincronizar rol de Spatie al crear usuario
        $this->syncSpatieRole($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Solo sincronizar si el campo 'role' cambió
        if ($user->isDirty('role')) {
            $this->syncSpatieRole($user);
        }
    }

    /**
     * Sincronizar rol básico con rol de Spatie
     */
    private function syncSpatieRole(User $user): void
    {
        $roleMapping = $this->getRoleMapping();

        // Obtener el rol de Spatie correspondiente
        $spatieRole = $roleMapping[$user->role] ?? null;

        if ($spatieRole) {
            // Sincronizar roles (esto elimina otros roles y asigna solo este)
            $user->syncRoles([$spatieRole]);

            Log::info("Usuario {$user->email} sincronizado con rol: {$spatieRole}");
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Opcional: Limpiar roles al eliminar usuario
        // $user->syncRoles([]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Restaurar rol al recuperar usuario soft-deleted
        $this->syncSpatieRole($user);
    }
}
