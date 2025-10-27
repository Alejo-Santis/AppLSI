<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateUsersRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:migrate-roles 
                            {--force : Forzar sincronización sin confirmación}
                            {--dry-run : Simular sin hacer cambios}';

    /**
     * The console command description.
     */
    protected $description = 'Migrar roles básicos de usuarios a roles de Spatie';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Iniciando migración de roles...');
        $this->newLine();

        // Obtener todos los usuarios
        $users = User::all();

        if ($users->isEmpty()) {
            $this->warn('No hay usuarios para migrar.');

            return Command::SUCCESS;
        }

        $this->info("📊 Se encontraron {$users->count()} usuarios");
        $this->newLine();

        // Confirmar antes de proceder
        if (! $dryRun && ! $force) {
            if (! $this->confirm('¿Deseas continuar con la migración?', true)) {
                $this->info('Operación cancelada.');

                return Command::SUCCESS;
            }
        }

        $roleMapping = [
            'admin' => 'Admin',
            'hr' => 'HR',
            'manager' => 'Manager',
            'employee' => 'Employee',
        ];

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        $this->withProgressBar($users, function ($user) use (&$migrated, &$skipped, &$errors, $roleMapping, $dryRun) {
            try {
                $spatieRole = $roleMapping[$user->role] ?? null;

                if (! $spatieRole) {
                    $this->newLine();
                    $this->warn("Usuario {$user->email}: Rol '{$user->role}' no válido");
                    $skipped++;

                    return;
                }

                // Verificar si ya tiene el rol
                if ($user->hasRole($spatieRole)) {
                    $skipped++;

                    return;
                }

                if (! $dryRun) {
                    // Asignar rol de Spatie
                    $user->syncRoles([$spatieRole]);
                }

                $migrated++;

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error con usuario {$user->email}: {$e->getMessage()}");
                $errors++;
            }
        });

        $this->newLine(2);
        $this->info('================================================');
        $this->info('📈 Resumen de Migración:');
        $this->info("   ✅ Migrados: {$migrated}");
        $this->info("   ⏭️  Omitidos: {$skipped}");
        $this->info("   ❌ Errores: {$errors}");
        $this->info('================================================');

        if ($dryRun) {
            $this->newLine();
            $this->warn('🔍 Modo DRY-RUN: No se realizaron cambios reales');
            $this->info('Ejecuta sin --dry-run para aplicar los cambios');
        }

        return Command::SUCCESS;
    }
}
