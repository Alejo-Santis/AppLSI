<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web', 'description' => 'Administrador del sistema'],
            ['name' => 'hr', 'guard_name' => 'web', 'description' => 'Recursos humanos'],
            ['name' => 'manager', 'guard_name' => 'web', 'description' => 'Jefe de departamento o proyecto'],
            ['name' => 'employee', 'guard_name' => 'web', 'description' => 'Empleado regular'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('✅ Roles creados correctamente.');
    }
}
