<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Creando usuario administrador...');

        // Verificar si ya existe un admin
        $existingAdmin = User::where('email', 'admin@example.com')->first();

        if ($existingAdmin) {
            $this->command->warn('⚠️  El usuario admin ya existe. Actualizando roles...');

            // Sincronizar rol de Spatie
            $existingAdmin->syncRoles(['admin']);

            $this->command->info('✅ Usuario admin actualizado correctamente');

            return;
        }

        // Crear usuario admin
        $admin = User::create([
            'name' => 'Administrador del Sistema',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // ⚠️ CAMBIAR EN PRODUCCIÓN
            'role' => 'admin', // Campo enum básico
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Asignar rol de Spatie
        $admin->assignRole('admin');

        $this->command->info('✅ Usuario administrador creado exitosamente');
        $this->command->info('');
        $this->command->info('📧 Email: admin@example.com');
        $this->command->info('🔑 Password: password');
        $this->command->warn('⚠️  IMPORTANTE: Cambia la contraseña en producción!');
    }
}
