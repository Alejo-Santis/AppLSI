<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Canales de Notificación Disponibles
    |--------------------------------------------------------------------------
    |
    | Define los canales de notificación que el sistema puede utilizar.
    | Asegúrate de configurar correctamente cada canal antes de usarlo.
    |
    */
    'channels' => [
        'database',  // Notificaciones en el dashboard (siempre activo)
        'mail',      // Notificaciones por email
        'sms',       // Notificaciones por SMS (requiere Twilio)
        'whatsapp',  // Notificaciones por WhatsApp (requiere Twilio)
    ],

    /*
    |--------------------------------------------------------------------------
    | Prioridad por Defecto
    |--------------------------------------------------------------------------
    |
    | Define la prioridad por defecto para notificaciones sin prioridad explícita.
    | Opciones: 'critical', 'important', 'informative', 'optional'
    |
    */
    'default_priority' => 'informative',

    /*
    |--------------------------------------------------------------------------
    | Configuración de Resúmenes (Digest)
    |--------------------------------------------------------------------------
    */
    'digest' => [
        'enabled' => true,
        'default_frequency' => 'daily', // 'immediate', 'hourly', 'daily', 'weekly'
        'default_time' => '09:00',
        'include_optional_by_default' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retención de Notificaciones
    |--------------------------------------------------------------------------
    |
    | Número de días para mantener notificaciones antes de eliminarlas.
    |
    */
    'retention_days' => 90,

    /*
    |--------------------------------------------------------------------------
    | Tamaño de Lotes
    |--------------------------------------------------------------------------
    |
    | Número máximo de notificaciones a procesar por lote.
    |
    */
    'batch_size' => 100,

    /*
    |--------------------------------------------------------------------------
    | Proveedor de SMS
    |--------------------------------------------------------------------------
    |
    | Opciones: 'twilio', 'nexmo'
    |
    */
    'sms_provider' => env('NOTIFICATION_SMS_PROVIDER', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp
    |--------------------------------------------------------------------------
    */
    'whatsapp_enabled' => env('NOTIFICATION_WHATSAPP_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Notificaciones Críticas Siempre Inmediatas
    |--------------------------------------------------------------------------
    |
    | Si está activado, las notificaciones críticas siempre se envían
    | inmediatamente, ignorando la configuración de resúmenes del usuario.
    |
    */
    'critical_always_immediate' => true,

    /*
    |--------------------------------------------------------------------------
    | Límites de Tasa (Rate Limits)
    |--------------------------------------------------------------------------
    |
    | Define el número máximo de notificaciones por canal por hora.
    |
    */
    'rate_limits' => [
        'email' => 50,
        'sms' => 10,
        'whatsapp' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Horario de No Molestar (Global)
    |--------------------------------------------------------------------------
    |
    | Horario en el que NO se envían SMS/WhatsApp (solo críticas se omiten).
    | Formato 24 horas.
    |
    */
    'quiet_hours' => [
        'enabled' => true,
        'start' => '22:00',
        'end' => '07:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Email
    |--------------------------------------------------------------------------
    */
    'email' => [
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@applsi.com'),
        'from_name' => env('MAIL_FROM_NAME', 'AppLSI - Sistema de RH'),
        'reply_to' => env('NOTIFICATION_REPLY_TO', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Twilio (SMS y WhatsApp)
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'phone_number' => env('TWILIO_PHONE_NUMBER'),
        'whatsapp_number' => env('TWILIO_WHATSAPP_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles que Reciben Notificaciones por Tipo
    |--------------------------------------------------------------------------
    |
    | Define qué roles reciben cada tipo de notificación por defecto.
    |
    */
    'role_notifications' => [
        // Empleados
        'employee_created' => ['hr', 'manager'],
        'employee_updated' => ['hr'],
        'employee_deleted' => ['hr', 'manager', 'admin'],
        'contract_expiring_30_days' => ['hr', 'admin'],
        'contract_expiring_7_days' => ['hr', 'admin'],
        'employee_birthday' => ['hr', 'manager'],
        'employee_anniversary' => ['hr', 'manager'],

        // Documentos
        'document_uploaded' => ['hr'],
        'document_approved' => [], // Solo al empleado dueño
        'document_rejected' => [], // Solo al empleado dueño
        'document_expiring_30_days' => ['hr'],
        'document_expiring_7_days' => ['hr', 'manager'],
        'document_expired' => ['hr', 'manager', 'admin'],
        'document_deleted' => ['hr', 'admin'],

        // Salarios
        'salary_created' => ['hr', 'admin'],
        'salary_updated' => ['hr', 'admin'],
        'salary_deleted' => ['hr', 'admin'],
        'salary_review_upcoming' => ['hr', 'manager'],

        // Proyectos
        'project_created' => ['manager', 'hr'],
        'employee_assigned_to_project' => [], // Al empleado asignado
        'employee_removed_from_project' => [], // Al empleado removido
        'project_deadline_approaching' => ['manager'],
        'project_overdue' => ['manager', 'admin'],

        // Contactos de Emergencia
        'emergency_contact_added' => ['hr'],
        'emergency_contact_updated' => ['hr'],
        'missing_emergency_contact' => ['hr', 'manager'],
        'emergency_contact_used' => ['hr', 'admin'],

        // Departamentos
        'department_created' => ['hr', 'admin'],
        'department_deleted' => ['hr', 'admin'],
        'position_deleted_with_employees' => ['hr', 'admin'],

        // Roles y Permisos
        'role_assigned' => [], // Al usuario afectado
        'role_removed' => [], // Al usuario afectado
        'permissions_changed' => [], // Al usuario afectado
        'unauthorized_access_attempt' => ['admin'],
        'login_from_new_location' => [], // Al usuario afectado

        // Sistema
        'bulk_import_completed' => ['hr'],
        'bulk_import_failed' => ['hr', 'admin'],
        'export_generated' => [], // Al usuario solicitante
        'backup_completed' => ['admin'],
        'backup_failed' => ['admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Websockets / Pusher (Opcional)
    |--------------------------------------------------------------------------
    */
    'realtime' => [
        'enabled' => env('NOTIFICATION_REALTIME_ENABLED', false),
        'driver' => env('BROADCAST_DRIVER', 'pusher'), // 'pusher' o 'reverb'
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => true,
        'channel' => 'daily', // Canal de log de Laravel
    ],
];