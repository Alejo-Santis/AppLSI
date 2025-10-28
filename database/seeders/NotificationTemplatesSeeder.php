<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // ========================================
            // MÓDULO DE EMPLEADOS
            // ========================================
            [
                'notification_type' => 'employee_created',
                'subject_email' => 'Nuevo Empleado Registrado - {{employee_name}}',
                'body_email' => '<p>Se ha registrado un nuevo empleado en el sistema:</p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li><li><strong>Puesto:</strong> {{position}}</li><li><strong>Fecha de ingreso:</strong> {{hire_date}}</li></ul><p>Registrado por: {{created_by}}</p>',
                'body_sms' => 'Nuevo empleado: {{employee_name}} - {{department}}',
                'body_whatsapp' => '🎉 *Nuevo Empleado*\n\nNombre: {{employee_name}}\nDepartamento: {{department}}\nPuesto: {{position}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'employee_updated',
                'subject_email' => 'Información de Empleado Actualizada - {{employee_name}}',
                'body_email' => '<p>Se ha actualizado la información del empleado:</p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Cambios:</strong> {{changes}}</li></ul><p>Actualizado por: {{updated_by}}</p>',
                'body_sms' => 'Info actualizada: {{employee_name}}',
                'body_whatsapp' => '✏️ *Empleado Actualizado*\n\n{{employee_name}}\nCambios: {{changes}}',
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'employee_deleted',
                'subject_email' => '⚠️ Baja de Empleado - {{employee_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>Se ha dado de baja al siguiente empleado:</strong></p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li><li><strong>Fecha de baja:</strong> {{termination_date}}</li><li><strong>Motivo:</strong> {{reason}}</li></ul><p>Dado de baja por: {{deleted_by}}</p>',
                'body_sms' => 'URGENTE: Baja de {{employee_name}}',
                'body_whatsapp' => '⚠️ *BAJA DE EMPLEADO*\n\n{{employee_name}}\nFecha: {{termination_date}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'contract_expiring_30_days',
                'subject_email' => '⚠️ Contrato por Vencer en 30 Días - {{employee_name}}',
                'body_email' => '<p><strong>El contrato del siguiente empleado vence en 30 días:</strong></p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li><li><strong>Fecha de vencimiento:</strong> {{expiration_date}}</li></ul><p>Por favor, gestione la renovación o finalización del contrato.</p>',
                'body_sms' => 'Contrato vence 30d: {{employee_name}}',
                'body_whatsapp' => '⏰ *Contrato por Vencer*\n\n{{employee_name}}\nVence: {{expiration_date}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'contract_expiring_7_days',
                'subject_email' => '🚨 URGENTE: Contrato Vence en 7 Días - {{employee_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>ATENCIÓN: El contrato vence en 7 días:</strong></p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li><li><strong>Fecha de vencimiento:</strong> {{expiration_date}}</li></ul><p style="color: #dc3545;"><strong>ACCIÓN INMEDIATA REQUERIDA</strong></p>',
                'body_sms' => 'URGENTE: Contrato {{employee_name}} vence en 7 dias',
                'body_whatsapp' => '🚨 *URGENTE - CONTRATO*\n\n{{employee_name}}\nVence: {{expiration_date}}\n\n⚠️ ACCIÓN INMEDIATA',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'employee_birthday',
                'subject_email' => '🎂 Cumpleaños de {{employee_name}}',
                'body_email' => '<p>Hoy es el cumpleaños de:</p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li></ul><p>¡No olvides felicitarlo/a!</p>',
                'body_sms' => 'Cumpleaños: {{employee_name}}',
                'body_whatsapp' => '🎂 *Cumpleaños*\n\n{{employee_name}}\n{{department}}',
                'priority' => 'optional',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'employee_anniversary',
                'subject_email' => '🎉 Aniversario Laboral - {{employee_name}}',
                'body_email' => '<p>Hoy se cumple el aniversario laboral de:</p><ul><li><strong>Nombre:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li><li><strong>Años en la empresa:</strong> {{years}}</li></ul>',
                'body_sms' => 'Aniversario: {{employee_name}} - {{years}} años',
                'body_whatsapp' => '🎉 *Aniversario Laboral*\n\n{{employee_name}}\n{{years}} años',
                'priority' => 'optional',
                'default_channels' => json_encode(['database']),
            ],

            // ========================================
            // MÓDULO DE DOCUMENTOS
            // ========================================
            [
                'notification_type' => 'document_uploaded',
                'subject_email' => 'Nuevo Documento Subido - {{document_name}}',
                'body_email' => '<p>Se ha subido un nuevo documento:</p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Tipo:</strong> {{document_type}}</li></ul><p>Subido por: {{uploaded_by}}</p>',
                'body_sms' => 'Documento subido: {{document_name}}',
                'body_whatsapp' => '📄 *Nuevo Documento*\n\n{{document_name}}\nEmpleado: {{employee_name}}',
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'document_approved',
                'subject_email' => '✅ Documento Aprobado - {{document_name}}',
                'body_email' => '<p style="color: #28a745;">Tu documento ha sido aprobado:</p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Aprobado por:</strong> {{approved_by}}</li><li><strong>Fecha:</strong> {{approval_date}}</li></ul>',
                'body_sms' => 'Doc aprobado: {{document_name}}',
                'body_whatsapp' => '✅ *Documento Aprobado*\n\n{{document_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'document_rejected',
                'subject_email' => '❌ Documento Rechazado - {{document_name}}',
                'body_email' => '<p style="color: #dc3545;">Tu documento ha sido rechazado:</p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Motivo:</strong> {{rejection_reason}}</li><li><strong>Rechazado por:</strong> {{rejected_by}}</li></ul><p>Por favor, corrige y vuelve a subir el documento.</p>',
                'body_sms' => 'Doc rechazado: {{document_name}}',
                'body_whatsapp' => '❌ *Documento Rechazado*\n\n{{document_name}}\nMotivo: {{rejection_reason}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'document_expiring_30_days',
                'subject_email' => '⚠️ Documento por Vencer en 30 Días - {{document_name}}',
                'body_email' => '<p>El siguiente documento vence en 30 días:</p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Fecha de vencimiento:</strong> {{expiration_date}}</li></ul><p>Por favor, renueve este documento a tiempo.</p>',
                'body_sms' => 'Doc vence 30d: {{document_name}}',
                'body_whatsapp' => '⏰ *Documento por Vencer*\n\n{{document_name}}\nVence: {{expiration_date}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'document_expiring_7_days',
                'subject_email' => '🚨 URGENTE: Documento Vence en 7 Días - {{document_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>ATENCIÓN: Documento vence en 7 días:</strong></p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Fecha de vencimiento:</strong> {{expiration_date}}</li></ul><p style="color: #dc3545;"><strong>RENUEVE ESTE DOCUMENTO INMEDIATAMENTE</strong></p>',
                'body_sms' => 'URGENTE: Doc {{document_name}} vence 7d',
                'body_whatsapp' => '🚨 *URGENTE - DOCUMENTO*\n\n{{document_name}}\nVence: {{expiration_date}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'document_expired',
                'subject_email' => '🚨 Documento VENCIDO - {{document_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>DOCUMENTO VENCIDO:</strong></p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Fecha de vencimiento:</strong> {{expiration_date}}</li></ul><p style="color: #dc3545;"><strong>ACCIÓN INMEDIATA REQUERIDA</strong></p>',
                'body_sms' => 'URGENTE: Doc {{document_name}} VENCIDO',
                'body_whatsapp' => '🚨 *DOCUMENTO VENCIDO*\n\n{{document_name}}\n⚠️ ACCIÓN INMEDIATA',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'document_deleted',
                'subject_email' => 'Documento Eliminado - {{document_name}}',
                'body_email' => '<p>Se ha eliminado un documento:</p><ul><li><strong>Documento:</strong> {{document_name}}</li><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Eliminado por:</strong> {{deleted_by}}</li><li><strong>Fecha:</strong> {{deletion_date}}</li></ul>',
                'body_sms' => 'Doc eliminado: {{document_name}}',
                'body_whatsapp' => '🗑️ *Documento Eliminado*\n\n{{document_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],

            // ========================================
            // MÓDULO DE SALARIOS
            // ========================================
            [
                'notification_type' => 'salary_created',
                'subject_email' => 'Nuevo Registro Salarial - {{employee_name}}',
                'body_email' => '<p>Se ha registrado un nuevo salario:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Nuevo salario:</strong> {{salary_amount}}</li><li><strong>Fecha efectiva:</strong> {{effective_date}}</li></ul><p>Registrado por: {{created_by}}</p>',
                'body_sms' => 'Nuevo salario: {{employee_name}}',
                'body_whatsapp' => '💰 *Nuevo Salario*\n\n{{employee_name}}\n{{salary_amount}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'salary_updated',
                'subject_email' => 'Cambio Salarial - {{employee_name}}',
                'body_email' => '<p>Se ha actualizado el salario:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Salario anterior:</strong> {{old_salary}}</li><li><strong>Nuevo salario:</strong> {{new_salary}}</li><li><strong>Diferencia:</strong> {{difference}}</li></ul><p>Actualizado por: {{updated_by}}</p>',
                'body_sms' => 'Salario actualizado: {{employee_name}}',
                'body_whatsapp' => '💰 *Cambio Salarial*\n\n{{employee_name}}\nNuevo: {{new_salary}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'salary_deleted',
                'subject_email' => '⚠️ Registro Salarial Eliminado - {{employee_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>Se ha eliminado un registro salarial:</strong></p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Salario eliminado:</strong> {{salary_amount}}</li><li><strong>Eliminado por:</strong> {{deleted_by}}</li></ul>',
                'body_sms' => 'ALERTA: Salario eliminado {{employee_name}}',
                'body_whatsapp' => '⚠️ *Salario Eliminado*\n\n{{employee_name}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'salary_review_upcoming',
                'subject_email' => 'Revisión Salarial Próxima - {{employee_name}}',
                'body_email' => '<p>Se acerca la fecha de revisión salarial:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Fecha de revisión:</strong> {{review_date}}</li><li><strong>Salario actual:</strong> {{current_salary}}</li></ul>',
                'body_sms' => 'Revisión salarial: {{employee_name}}',
                'body_whatsapp' => '📅 *Revisión Salarial*\n\n{{employee_name}}\n{{review_date}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],

            // ========================================
            // MÓDULO DE PROYECTOS
            // ========================================
            [
                'notification_type' => 'project_created',
                'subject_email' => 'Nuevo Proyecto Creado - {{project_name}}',
                'body_email' => '<p>Se ha creado un nuevo proyecto:</p><ul><li><strong>Proyecto:</strong> {{project_name}}</li><li><strong>Descripción:</strong> {{description}}</li><li><strong>Fecha de inicio:</strong> {{start_date}}</li><li><strong>Fecha de fin:</strong> {{end_date}}</li></ul><p>Creado por: {{created_by}}</p>',
                'body_sms' => 'Nuevo proyecto: {{project_name}}',
                'body_whatsapp' => '🚀 *Nuevo Proyecto*\n\n{{project_name}}\nInicio: {{start_date}}',
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'employee_assigned_to_project',
                'subject_email' => 'Asignado a Proyecto - {{project_name}}',
                'body_email' => '<p>Has sido asignado a un proyecto:</p><ul><li><strong>Proyecto:</strong> {{project_name}}</li><li><strong>Rol:</strong> {{role}}</li><li><strong>Fecha de inicio:</strong> {{start_date}}</li><li><strong>Gerente del proyecto:</strong> {{project_manager}}</li></ul>',
                'body_sms' => 'Asignado a: {{project_name}}',
                'body_whatsapp' => '✅ *Asignado a Proyecto*\n\n{{project_name}}\nRol: {{role}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'employee_removed_from_project',
                'subject_email' => 'Removido de Proyecto - {{project_name}}',
                'body_email' => '<p>Has sido removido del siguiente proyecto:</p><ul><li><strong>Proyecto:</strong> {{project_name}}</li><li><strong>Fecha de remoción:</strong> {{removal_date}}</li><li><strong>Motivo:</strong> {{reason}}</li></ul>',
                'body_sms' => 'Removido de: {{project_name}}',
                'body_whatsapp' => '❌ *Removido de Proyecto*\n\n{{project_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'project_deadline_approaching',
                'subject_email' => '⏰ Proyecto por Finalizar - {{project_name}}',
                'body_email' => '<p>El proyecto está próximo a finalizar:</p><ul><li><strong>Proyecto:</strong> {{project_name}}</li><li><strong>Fecha de fin:</strong> {{end_date}}</li><li><strong>Días restantes:</strong> {{days_remaining}}</li><li><strong>Progreso:</strong> {{progress}}%</li></ul>',
                'body_sms' => 'Proyecto {{project_name}} finaliza en {{days_remaining}}d',
                'body_whatsapp' => '⏰ *Proyecto por Finalizar*\n\n{{project_name}}\n{{days_remaining}} días',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'project_overdue',
                'subject_email' => '🚨 Proyecto ATRASADO - {{project_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>ALERTA: Proyecto atrasado:</strong></p><ul><li><strong>Proyecto:</strong> {{project_name}}</li><li><strong>Fecha límite original:</strong> {{original_end_date}}</li><li><strong>Días de retraso:</strong> {{days_overdue}}</li><li><strong>Progreso:</strong> {{progress}}%</li></ul>',
                'body_sms' => 'URGENTE: {{project_name}} ATRASADO',
                'body_whatsapp' => '🚨 *PROYECTO ATRASADO*\n\n{{project_name}}\n{{days_overdue}} días',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],

            // ========================================
            // MÓDULO DE CONTACTOS DE EMERGENCIA
            // ========================================
            [
                'notification_type' => 'emergency_contact_added',
                'subject_email' => 'Contacto de Emergencia Agregado - {{employee_name}}',
                'body_email' => '<p>Se ha agregado un contacto de emergencia:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Contacto:</strong> {{contact_name}}</li><li><strong>Relación:</strong> {{relationship}}</li><li><strong>Teléfono:</strong> {{phone}}</li></ul>',
                'body_sms' => 'Contacto emergencia: {{employee_name}}',
                'body_whatsapp' => '📞 *Contacto de Emergencia*\n\n{{employee_name}}\n{{contact_name}}',
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'missing_emergency_contact',
                'subject_email' => '⚠️ Sin Contacto de Emergencia - {{employee_name}}',
                'body_email' => '<p>El empleado no tiene contacto de emergencia registrado:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Departamento:</strong> {{department}}</li></ul><p>Por favor, solicite al empleado que registre un contacto de emergencia.</p>',
                'body_sms' => 'Sin contacto emergencia: {{employee_name}}',
                'body_whatsapp' => '⚠️ *Sin Contacto de Emergencia*\n\n{{employee_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'emergency_contact_used',
                'subject_email' => '🚨 EMERGENCIA: Contacto Utilizado - {{employee_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>ALERTA DE EMERGENCIA:</strong></p><p>Se ha utilizado el contacto de emergencia de:</p><ul><li><strong>Empleado:</strong> {{employee_name}}</li><li><strong>Contacto notificado:</strong> {{contact_name}}</li><li><strong>Fecha y hora:</strong> {{incident_datetime}}</li><li><strong>Motivo:</strong> {{reason}}</li></ul>',
                'body_sms' => 'EMERGENCIA: {{employee_name}}',
                'body_whatsapp' => '🚨 *EMERGENCIA*\n\n{{employee_name}}\n{{incident_datetime}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],

            // ========================================
            // MÓDULO DE DEPARTAMENTOS
            // ========================================
            [
                'notification_type' => 'department_created',
                'subject_email' => 'Nuevo Departamento Creado - {{department_name}}',
                'body_email' => '<p>Se ha creado un nuevo departamento:</p><ul><li><strong>Departamento:</strong> {{department_name}}</li><li><strong>Descripción:</strong> {{description}}</li><li><strong>Creado por:</strong> {{created_by}}</li></ul>',
                'body_sms' => 'Nuevo depto: {{department_name}}',
                'body_whatsapp' => '🏢 *Nuevo Departamento*\n\n{{department_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'department_deleted',
                'subject_email' => '⚠️ Departamento Eliminado - {{department_name}}',
                'body_email' => '<p style="color: #dc3545;"><strong>Se ha eliminado un departamento:</strong></p><ul><li><strong>Departamento:</strong> {{department_name}}</li><li><strong>Empleados afectados:</strong> {{affected_employees}}</li><li><strong>Eliminado por:</strong> {{deleted_by}}</li></ul>',
                'body_sms' => 'ALERTA: Depto {{department_name}} eliminado',
                'body_whatsapp' => '⚠️ *Departamento Eliminado*\n\n{{department_name}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'position_deleted_with_employees',
                'subject_email' => '🚨 Puesto Eliminado con Empleados Asignados',
                'body_email' => '<p style="color: #dc3545;"><strong>ALERTA: Se eliminó un puesto con empleados:</strong></p><ul><li><strong>Puesto:</strong> {{position_name}}</li><li><strong>Empleados afectados:</strong> {{affected_count}}</li><li><strong>Eliminado por:</strong> {{deleted_by}}</li></ul><p>Se requiere reasignación inmediata.</p>',
                'body_sms' => 'URGENTE: Puesto eliminado con empleados',
                'body_whatsapp' => '🚨 *PUESTO ELIMINADO*\n\nEmpleados afectados: {{affected_count}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],

            // ========================================
            // MÓDULO DE ROLES Y PERMISOS
            // ========================================
            [
                'notification_type' => 'role_assigned',
                'subject_email' => 'Nuevo Rol Asignado - {{role_name}}',
                'body_email' => '<p>Se te ha asignado un nuevo rol:</p><ul><li><strong>Rol:</strong> {{role_name}}</li><li><strong>Permisos:</strong> {{permissions_count}}</li><li><strong>Asignado por:</strong> {{assigned_by}}</li></ul>',
                'body_sms' => 'Rol asignado: {{role_name}}',
                'body_whatsapp' => '🔑 *Rol Asignado*\n\n{{role_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'role_removed',
                'subject_email' => '⚠️ Rol Removido - {{role_name}}',
                'body_email' => '<p style="color: #dc3545;">Se te ha removido un rol:</p><ul><li><strong>Rol:</strong> {{role_name}}</li><li><strong>Removido por:</strong> {{removed_by}}</li><li><strong>Fecha:</strong> {{removal_date}}</li></ul>',
                'body_sms' => 'Rol removido: {{role_name}}',
                'body_whatsapp' => '⚠️ *Rol Removido*\n\n{{role_name}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'permissions_changed',
                'subject_email' => 'Permisos Modificados en tu Rol',
                'body_email' => '<p>Los permisos de tu rol han sido modificados:</p><ul><li><strong>Rol:</strong> {{role_name}}</li><li><strong>Permisos agregados:</strong> {{added_permissions}}</li><li><strong>Permisos removidos:</strong> {{removed_permissions}}</li></ul>',
                'body_sms' => 'Permisos modificados: {{role_name}}',
                'body_whatsapp' => '🔐 *Permisos Modificados*\n\n{{role_name}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'unauthorized_access_attempt',
                'subject_email' => '🚨 SEGURIDAD: Intento de Acceso No Autorizado',
                'body_email' => '<p style="color: #dc3545;"><strong>ALERTA DE SEGURIDAD:</strong></p><ul><li><strong>Usuario:</strong> {{user_name}}</li><li><strong>Recurso:</strong> {{resource}}</li><li><strong>IP:</strong> {{ip_address}}</li><li><strong>Fecha y hora:</strong> {{attempt_datetime}}</li></ul>',
                'body_sms' => 'SEGURIDAD: Acceso no autorizado detectado',
                'body_whatsapp' => '🚨 *ALERTA DE SEGURIDAD*\n\nAcceso no autorizado',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'login_from_new_location',
                'subject_email' => '🔐 Nuevo Inicio de Sesión Detectado',
                'body_email' => '<p>Se ha detectado un inicio de sesión desde una nueva ubicación:</p><ul><li><strong>Usuario:</strong> {{user_name}}</li><li><strong>Ubicación:</strong> {{location}}</li><li><strong>Dispositivo:</strong> {{device}}</li><li><strong>IP:</strong> {{ip_address}}</li><li><strong>Fecha y hora:</strong> {{login_datetime}}</li></ul><p>Si no fuiste tú, cambia tu contraseña inmediatamente.</p>',
                'body_sms' => 'Nuevo login: {{location}}',
                'body_whatsapp' => '🔐 *Nuevo Inicio de Sesión*\n\n{{location}}\n{{device}}',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],

            // ========================================
            // SISTEMA
            // ========================================
            [
                'notification_type' => 'bulk_import_completed',
                'subject_email' => '✅ Importación Masiva Completada',
                'body_email' => '<p style="color: #28a745;">La importación masiva se ha completado exitosamente:</p><ul><li><strong>Tipo:</strong> {{import_type}}</li><li><strong>Registros importados:</strong> {{imported_count}}</li><li><strong>Registros con errores:</strong> {{error_count}}</li></ul>',
                'body_sms' => 'Importación completada: {{imported_count}} registros',
                'body_whatsapp' => '✅ *Importación Completada*\n\n{{imported_count}} registros',
                'priority' => 'important',
                'default_channels' => json_encode(['email', 'database']),
            ],
            [
                'notification_type' => 'bulk_import_failed',
                'subject_email' => '❌ Error en Importación Masiva',
                'body_email' => '<p style="color: #dc3545;"><strong>La importación masiva ha fallado:</strong></p><ul><li><strong>Tipo:</strong> {{import_type}}</li><li><strong>Error:</strong> {{error_message}}</li><li><strong>Archivo:</strong> {{filename}}</li></ul>',
                'body_sms' => 'ERROR: Importación fallida',
                'body_whatsapp' => '❌ *Error en Importación*\n\n{{import_type}}',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
            [
                'notification_type' => 'export_generated',
                'subject_email' => '📊 Exportación Lista para Descargar',
                'body_email' => '<p>Tu exportación está lista:</p><ul><li><strong>Tipo:</strong> {{export_type}}</li><li><strong>Registros:</strong> {{record_count}}</li><li><strong>Formato:</strong> {{format}}</li></ul><p><a href="{{download_url}}">Descargar ahora</a></p>',
                'body_sms' => 'Exportación lista',
                'body_whatsapp' => '📊 *Exportación Lista*\n\n{{export_type}}',
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'backup_completed',
                'subject_email' => '✅ Backup del Sistema Completado',
                'body_email' => '<p style="color: #28a745;">El backup del sistema se ha completado exitosamente:</p><ul><li><strong>Fecha:</strong> {{backup_date}}</li><li><strong>Tamaño:</strong> {{backup_size}}</li><li><strong>Ubicación:</strong> {{backup_location}}</li></ul>',
                'body_sms' => null,
                'body_whatsapp' => null,
                'priority' => 'informative',
                'default_channels' => json_encode(['database']),
            ],
            [
                'notification_type' => 'backup_failed',
                'subject_email' => '🚨 CRÍTICO: Fallo en Backup del Sistema',
                'body_email' => '<p style="color: #dc3545;"><strong>ALERTA CRÍTICA: El backup ha fallado:</strong></p><ul><li><strong>Fecha del intento:</strong> {{attempt_date}}</li><li><strong>Error:</strong> {{error_message}}</li></ul><p style="color: #dc3545;"><strong>ACCIÓN INMEDIATA REQUERIDA</strong></p>',
                'body_sms' => 'CRÍTICO: Backup fallido',
                'body_whatsapp' => '🚨 *BACKUP FALLIDO*\n\n⚠️ ACCIÓN INMEDIATA',
                'priority' => 'critical',
                'default_channels' => json_encode(['email', 'database', 'sms']),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->insert($template);
        }

        $this->command->info('✅ Plantillas de notificaciones creadas correctamente.');
    }
}
