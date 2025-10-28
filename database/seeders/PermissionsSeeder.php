<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'description' => 'Ver dashboard', 'module' => 'Dashboard'],

            // Perfil de Usuario
            ['name' => 'profile.view', 'description' => 'Ver perfil propio', 'module' => 'Perfil'],
            ['name' => 'profile.edit', 'description' => 'Editar perfil propio', 'module' => 'Perfil'],
            ['name' => 'profile.avatar.delete', 'description' => 'Eliminar avatar propio', 'module' => 'Perfil'],
            ['name' => 'account.view', 'description' => 'Ver información de cuenta', 'module' => 'Perfil'],
            ['name' => 'account.edit', 'description' => 'Editar información de cuenta', 'module' => 'Perfil'],
            ['name' => 'password.change', 'description' => 'Cambiar contraseña propia', 'module' => 'Perfil'],

            // Departamentos
            ['name' => 'departments.view', 'description' => 'Ver listado de departamentos', 'module' => 'Departamentos'],
            ['name' => 'departments.show', 'description' => 'Ver detalle de departamento', 'module' => 'Departamentos'],
            ['name' => 'departments.create', 'description' => 'Crear departamento', 'module' => 'Departamentos'],
            ['name' => 'departments.edit', 'description' => 'Editar departamento', 'module' => 'Departamentos'],
            ['name' => 'departments.delete', 'description' => 'Eliminar departamento', 'module' => 'Departamentos'],
            ['name' => 'departments.export', 'description' => 'Exportar departamentos', 'module' => 'Departamentos'],
            ['name' => 'departments.import', 'description' => 'Importar departamentos', 'module' => 'Departamentos'],

            // Posiciones
            ['name' => 'positions.view', 'description' => 'Ver listado de posiciones', 'module' => 'Posiciones'],
            ['name' => 'positions.show', 'description' => 'Ver detalle de posición', 'module' => 'Posiciones'],
            ['name' => 'positions.create', 'description' => 'Crear posición', 'module' => 'Posiciones'],
            ['name' => 'positions.edit', 'description' => 'Editar posición', 'module' => 'Posiciones'],
            ['name' => 'positions.delete', 'description' => 'Eliminar posición', 'module' => 'Posiciones'],
            ['name' => 'positions.export', 'description' => 'Exportar posiciones', 'module' => 'Posiciones'],
            ['name' => 'positions.import', 'description' => 'Importar posiciones', 'module' => 'Posiciones'],

            // Empleados
            ['name' => 'employees.view', 'description' => 'Ver listado de empleados', 'module' => 'Empleados'],
            ['name' => 'employees.view-all', 'description' => 'Ver todos los empleados', 'module' => 'Empleados'],
            ['name' => 'employees.view-department', 'description' => 'Ver empleados de su departamento', 'module' => 'Empleados'],
            ['name' => 'employees.show', 'description' => 'Ver detalle de empleado', 'module' => 'Empleados'],
            ['name' => 'employees.create', 'description' => 'Crear empleado', 'module' => 'Empleados'],
            ['name' => 'employees.edit', 'description' => 'Editar empleado', 'module' => 'Empleados'],
            ['name' => 'employees.delete', 'description' => 'Eliminar empleado', 'module' => 'Empleados'],
            ['name' => 'employees.export', 'description' => 'Exportar empleados', 'module' => 'Empleados'],
            ['name' => 'employees.import', 'description' => 'Importar empleados', 'module' => 'Empleados'],

            // Documentos
            ['name' => 'documents.view', 'description' => 'Ver documentos', 'module' => 'Documentos'],
            ['name' => 'documents.view-own', 'description' => 'Ver documentos propios', 'module' => 'Documentos'],
            ['name' => 'documents.create', 'description' => 'Subir documentos', 'module' => 'Documentos'],
            ['name' => 'documents.download', 'description' => 'Descargar documentos', 'module' => 'Documentos'],
            ['name' => 'documents.preview', 'description' => 'Previsualizar documentos', 'module' => 'Documentos'],
            ['name' => 'documents.delete', 'description' => 'Eliminar documentos', 'module' => 'Documentos'],

            // Contactos de Emergencia
            ['name' => 'contacts.view', 'description' => 'Ver contactos de emergencia', 'module' => 'Contactos'],
            ['name' => 'contacts.view-own', 'description' => 'Ver contactos propios', 'module' => 'Contactos'],
            ['name' => 'contacts.create', 'description' => 'Crear contacto de emergencia', 'module' => 'Contactos'],
            ['name' => 'contacts.edit', 'description' => 'Editar contacto de emergencia', 'module' => 'Contactos'],
            ['name' => 'contacts.delete', 'description' => 'Eliminar contacto de emergencia', 'module' => 'Contactos'],

            // Historial Salarial
            ['name' => 'salary-history.view', 'description' => 'Ver historial salarial', 'module' => 'Salarios'],
            ['name' => 'salary-history.view-own', 'description' => 'Ver historial salarial propio', 'module' => 'Salarios'],
            ['name' => 'salary-history.create', 'description' => 'Registrar cambio salarial', 'module' => 'Salarios'],
            ['name' => 'salary-history.delete', 'description' => 'Eliminar registro salarial', 'module' => 'Salarios'],

            // Proyectos
            ['name' => 'projects.view', 'description' => 'Ver listado de proyectos', 'module' => 'Proyectos'],
            ['name' => 'projects.view-all', 'description' => 'Ver todos los proyectos', 'module' => 'Proyectos'],
            ['name' => 'projects.view-assigned', 'description' => 'Ver proyectos asignados', 'module' => 'Proyectos'],
            ['name' => 'projects.show', 'description' => 'Ver detalle de proyecto', 'module' => 'Proyectos'],
            ['name' => 'projects.create', 'description' => 'Crear proyecto', 'module' => 'Proyectos'],
            ['name' => 'projects.edit', 'description' => 'Editar proyecto', 'module' => 'Proyectos'],
            ['name' => 'projects.delete', 'description' => 'Eliminar proyecto', 'module' => 'Proyectos'],
            ['name' => 'projects.assign-employees', 'description' => 'Asignar empleados a proyectos', 'module' => 'Proyectos'],
            ['name' => 'projects.remove-employees', 'description' => 'Remover empleados de proyectos', 'module' => 'Proyectos'],
            ['name' => 'projects.export', 'description' => 'Exportar proyectos', 'module' => 'Proyectos'],
            ['name' => 'projects.import', 'description' => 'Importar proyectos', 'module' => 'Proyectos'],

            // Roles y Permisos (Administración)
            ['name' => 'roles.view', 'description' => 'Ver roles', 'module' => 'Administración'],
            ['name' => 'roles.create', 'description' => 'Crear rol', 'module' => 'Administración'],
            ['name' => 'roles.edit', 'description' => 'Editar rol', 'module' => 'Administración'],
            ['name' => 'roles.delete', 'description' => 'Eliminar rol', 'module' => 'Administración'],
            ['name' => 'permissions.view', 'description' => 'Ver permisos', 'module' => 'Administración'],
            ['name' => 'permissions.assign', 'description' => 'Asignar permisos', 'module' => 'Administración'],
            ['name' => 'users.manage-roles', 'description' => 'Gestionar roles de usuarios', 'module' => 'Administración'],
            ['name' => 'users.manage-permissions', 'description' => 'Gestionar permisos de usuarios', 'module' => 'Administración'],

            // Notificaciones
            ['name' => 'notifications.view', 'description' => 'Ver notificaciones propias', 'module' => 'Notificaciones'],
            ['name' => 'notifications.view-all', 'description' => 'Ver todas las notificaciones', 'module' => 'Notificaciones'],
            ['name' => 'notifications.mark-read', 'description' => 'Marcar como leídas', 'module' => 'Notificaciones'],
            ['name' => 'notifications.delete', 'description' => 'Eliminar notificaciones propias', 'module' => 'Notificaciones'],
            ['name' => 'notifications.preferences', 'description' => 'Configurar preferencias', 'module' => 'Notificaciones'],
            ['name' => 'notifications.send-manual', 'description' => 'Enviar notificaciones manuales', 'module' => 'Notificaciones'],
            ['name' => 'notifications.manage-templates', 'description' => 'Gestionar plantillas', 'module' => 'Notificaciones'],
            ['name' => 'notifications.view-analytics', 'description' => 'Ver analytics de notificaciones', 'module' => 'Notificaciones'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $this->command->info('✅ Permisos creados correctamente.');
    }
}
