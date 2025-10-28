# Plan de Implementación de Sistema de Notificaciones para AppLSI

## 📋 Información del Proyecto

**Proyecto:** AppLSI - Sistema de Gestión de Recursos Humanos  
**Stack Tecnológico:** Laravel 12 + Inertia.js + Svelte 5 + PostgreSQL + Bootstrap 5  
**Fecha de Creación:** 27 de Octubre, 2025  
**Versión del Plan:** 1.0

---

## 🎯 Objetivo General

Implementar un sistema completo y profesional de notificaciones que incluya:
- ✅ Notificaciones in-app (dashboard con campana en header)
- ✅ Notificaciones por email
- ✅ Notificaciones por SMS/WhatsApp (opcional, solo críticas)
- ✅ Sistema de preferencias de usuario
- ✅ Analytics y métricas
- ✅ Sistema de resúmenes (digest)

---

## 📊 Usuarios del Sistema

### ❌ NO utilizarán el sistema:
- **Empleados regulares** (nivel operativo)

### ✅ SÍ utilizarán el sistema:
1. **Super Admin / Administrador del Sistema (IT)**
2. **Administrador de RH**
3. **Gerente General / Mandos Altos**
4. **Gerente de Área / Mandos Medios**
5. **Jefe de Departamento**
6. **Personal de Finanzas**

---

## 🔔 Matriz de Clasificación de Notificaciones

### Criterios de Prioridad:

| Prioridad | Email | Dashboard | SMS/WhatsApp | Descripción |
|-----------|-------|-----------|--------------|-------------|
| 🔴 **CRÍTICO** | ✅ | ✅ | ✅ | Requiere acción inmediata |
| 🟠 **IMPORTANTE** | ✅ | ✅ | ❌ | Requiere atención pronto |
| 🟡 **INFORMATIVO** | ❌ | ✅ | ❌ | Solo para conocimiento |
| ⚪ **OPCIONAL** | ❌ | ✅ | ❌ | Puede desactivarse |

---

## 📦 1. MÓDULO DE EMPLEADOS

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Nuevo empleado creado | RH + Gerente del Depto | 🟠 IMPORTANTE | Preparación de recursos y onboarding |
| Empleado editado (datos sensibles) | RH + Empleado afectado | 🟡 INFORMATIVO | Trazabilidad de cambios |
| Empleado dado de baja | RH + Gerente + Finanzas | 🔴 CRÍTICO | Impacto en nómina, accesos, equipos |
| Cumpleaños de empleado | RH + Gerente directo | ⚪ OPCIONAL | Clima laboral |
| Aniversario laboral | RH + Gerente directo | ⚪ OPCIONAL | Reconocimiento y retención |
| Vencimiento de contrato (30 días) | RH + Gerente General | 🔴 CRÍTICO | Decisión de renovación |
| Vencimiento de contrato (7 días) | RH + Gerente General + Legal | 🔴 CRÍTICO | Urgencia máxima |

---

## 📄 2. MÓDULO DE DOCUMENTOS

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Documento subido | RH | 🟡 INFORMATIVO | Registro de actividad |
| Documento aprobado | Empleado relacionado | 🟠 IMPORTANTE | Confirma validación |
| Documento rechazado | Empleado relacionado + RH | 🟠 IMPORTANTE | Requiere corrección |
| Documento próximo a vencer (30 días) | RH + Empleado | 🟠 IMPORTANTE | Certificaciones, licencias |
| Documento próximo a vencer (7 días) | RH + Empleado + Gerente | 🔴 CRÍTICO | Riesgo legal |
| Documento vencido | RH + Gerente + Legal | 🔴 CRÍTICO | Incumplimiento activo |
| Documento eliminado | RH + Admin | 🟠 IMPORTANTE | Auditoría |

---

## 💰 3. MÓDULO DE HISTORIAL SALARIAL

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Nuevo salario registrado | Finanzas + RH | 🔴 CRÍTICO | Impacto presupuestal |
| Aumento salarial | Empleado + Finanzas + RH | 🔴 CRÍTICO | Información sensible |
| Salario editado | Finanzas + RH + Auditoría | 🔴 CRÍTICO | Trazabilidad financiera |
| Salario eliminado | Finanzas + RH + Auditoría | 🔴 CRÍTICO | Alto riesgo de error |
| Próxima revisión salarial (30 días) | RH + Gerente directo | 🟠 IMPORTANTE | Planificación presupuestal |

---

## 🚀 4. MÓDULO DE PROYECTOS

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Proyecto creado | Gerentes + RH | 🟡 INFORMATIVO | Visibilidad organizacional |
| Empleado asignado a proyecto | Empleado + Gerente proyecto + Gerente directo | 🟠 IMPORTANTE | Coordinar disponibilidad |
| Empleado removido de proyecto | Empleado + Gerente proyecto + Gerente directo | 🟠 IMPORTANTE | Reasignación de recursos |
| Proyecto próximo a finalizar (7 días) | Gerente proyecto + Empleados | 🟠 IMPORTANTE | Preparar cierre |
| Proyecto finalizado | Gerente proyecto + RH | 🟡 INFORMATIVO | Liberación de recursos |
| Proyecto atrasado | Gerente proyecto + Gerencia General | 🔴 CRÍTICO | Escalamiento |

---

## 🆘 5. MÓDULO DE CONTACTOS DE EMERGENCIA

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Contacto agregado | RH | 🟡 INFORMATIVO | Registro |
| Contacto actualizado | RH | 🟡 INFORMATIVO | Datos actualizados |
| Sin contacto de emergencia | RH + Gerente directo | 🟠 IMPORTANTE | Seguridad laboral |
| Contacto utilizado en emergencia | RH + Gerente General + Legal | 🔴 CRÍTICO | Incidente grave |

---

## 🏢 6. MÓDULO DE DEPARTAMENTOS Y PUESTOS

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Departamento creado | Gerencia General + RH | 🟠 IMPORTANTE | Cambio estructural |
| Departamento eliminado | Gerencia General + RH + Finanzas | 🔴 CRÍTICO | Impacto organizacional |
| Puesto creado | RH | 🟡 INFORMATIVO | Catálogo de puestos |
| Puesto eliminado con empleados | RH + Gerencia General | 🔴 CRÍTICO | Requiere reasignación |

---

## 🔐 7. MÓDULO DE ROLES Y PERMISOS

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Rol asignado a usuario | Usuario + Admin sistema | 🟠 IMPORTANTE | Seguridad de accesos |
| Rol removido de usuario | Usuario + Admin sistema | 🔴 CRÍTICO | Pérdida de acceso |
| Permisos modificados en rol | Usuarios con rol + Admin | 🟠 IMPORTANTE | Cambio en capacidades |
| Intento de acceso no autorizado | Admin sistema + Seguridad TI | 🔴 CRÍTICO | Brecha de seguridad |

---

## ⚙️ 8. NOTIFICACIONES DEL SISTEMA

| Acción | Destinatarios | Prioridad | Justificación |
|--------|---------------|-----------|---------------|
| Importación masiva exitosa | Usuario + RH | 🟠 IMPORTANTE | Confirmar operación |
| Importación masiva con errores | Usuario + RH + Admin | 🔴 CRÍTICO | Requiere corrección |
| Exportación generada | Usuario solicitante | 🟡 INFORMATIVO | Descarga disponible |
| Backup realizado | Admin sistema | 🟡 INFORMATIVO | Mantenimiento |
| Fallo en backup | Admin sistema + TI | 🔴 CRÍTICO | Riesgo de datos |
| Login desde nueva ubicación | Usuario + Admin | 🟠 IMPORTANTE | Seguridad |

---

## 📱 Canales de Notificación

### EMAIL (Formal y Documentado)
**Usar para:**
- ✅ Cambios en salarios
- ✅ Altas/Bajas de empleados
- ✅ Vencimientos de documentos importantes
- ✅ Asignaciones a proyectos
- ✅ Cambios de roles/permisos
- ✅ Todas las notificaciones críticas e importantes

### DASHBOARD (Inmediato y Accionable)
**Usar para:**
- ✅ TODAS las notificaciones
- ✅ Centro de notificaciones con badge contador
- ✅ Historial completo
- ✅ Marcar como leído/no leído
- ✅ Filtrar por tipo/módulo

### SMS/WhatsApp (Urgente y Personal)
**Usar SOLO para:**
- ✅ Vencimiento de contrato (7 días)
- ✅ Documentos vencidos (legales/críticos)
- ✅ Emergencias (uso de contacto de emergencia)
- ✅ Fallo en sistemas críticos
- ✅ Intentos de acceso sospechosos

**NO usar para:**
- ❌ Notificaciones informativas
- ❌ Cumpleaños o aniversarios
- ❌ Actualizaciones menores

---

## 🗄️ Estructura de Base de Datos

### 1. Tabla: `notifications` (Nativa de Laravel)
```sql
- id (uuid)
- type (string) → Clase de la notificación
- notifiable_type (string) → Modelo del receptor
- notifiable_id (bigint) → ID del receptor
- data (json) → Datos de la notificación
- read_at (timestamp nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### 2. Tabla: `notification_preferences` (Nueva)
```sql
- id
- user_id (FK users)
- notification_type (string)
- channel_email (boolean)
- channel_database (boolean)
- channel_sms (boolean)
- is_enabled (boolean)
- created_at
- updated_at

UNIQUE KEY (user_id, notification_type)
INDEX (user_id, notification_type)
```

### 3. Tabla: `notification_templates` (Nueva)
```sql
- id
- notification_type (string UNIQUE)
- subject_email (string)
- body_email (text) → Usa variables {{ }}
- body_sms (string 160 chars)
- body_whatsapp (text)
- priority (enum: 'critical', 'important', 'informative', 'optional')
- default_channels (json) → ['email', 'database', 'sms']
- created_at
- updated_at
```

### 4. Tabla: `notification_logs` (Nueva - Auditoría)
```sql
- id
- notification_id (FK notifications nullable)
- user_id (FK users)
- notification_type (string)
- channel (enum: 'email', 'database', 'sms', 'whatsapp')
- status (enum: 'sent', 'failed', 'pending')
- sent_at (timestamp)
- read_at (timestamp nullable)
- metadata (json) → IP, user agent, etc
- error_message (text nullable)
- created_at
- updated_at

INDEX (user_id, notification_type)
INDEX (status, sent_at)
```

### 5. Tabla: `notification_digest_settings` (Nueva)
```sql
- id
- user_id (FK users UNIQUE)
- digest_frequency (enum: 'immediate', 'hourly', 'daily', 'weekly')
- digest_time (time)
- include_optional (boolean)
- last_digest_sent_at (timestamp nullable)
- created_at
- updated_at
```

---

## 🏗️ Arquitectura del Sistema

### Capa 1: Events (Eventos)
```
App/Events/
├── Employee/
│   ├── EmployeeCreated.php
│   ├── EmployeeUpdated.php
│   ├── EmployeeDeleted.php
│   ├── EmployeeBirthdayUpcoming.php
│   ├── EmployeeAnniversaryUpcoming.php
│   ├── ContractExpiringIn30Days.php
│   └── ContractExpiringIn7Days.php
│
├── Document/
│   ├── DocumentUploaded.php
│   ├── DocumentApproved.php
│   ├── DocumentRejected.php
│   ├── DocumentExpiringIn30Days.php
│   ├── DocumentExpiringIn7Days.php
│   └── DocumentExpired.php
│
├── Salary/
│   ├── SalaryCreated.php
│   ├── SalaryUpdated.php
│   ├── SalaryDeleted.php
│   └── SalaryReviewUpcoming.php
│
├── Project/
│   ├── ProjectCreated.php
│   ├── ProjectUpdated.php
│   ├── EmployeeAssignedToProject.php
│   ├── EmployeeRemovedFromProject.php
│   ├── ProjectDeadlineApproaching.php
│   └── ProjectOverdue.php
│
├── Contact/
│   ├── EmergencyContactAdded.php
│   ├── EmergencyContactUpdated.php
│   ├── EmergencyContactUsed.php
│   └── MissingEmergencyContact.php
│
├── Department/
│   ├── DepartmentCreated.php
│   ├── DepartmentDeleted.php
│   └── PositionDeletedWithEmployees.php
│
├── Auth/
│   ├── RoleAssigned.php
│   ├── RoleRemoved.php
│   ├── PermissionsChanged.php
│   ├── UnauthorizedAccessAttempt.php
│   └── LoginFromNewLocation.php
│
└── System/
    ├── BulkImportCompleted.php
    ├── BulkImportFailed.php
    ├── ExportGenerated.php
    ├── BackupCompleted.php
    └── BackupFailed.php
```

### Capa 2: Listeners (Escuchadores de Eventos)
```
App/Listeners/
├── Employee/
│   ├── SendEmployeeCreatedNotifications.php
│   ├── SendEmployeeDeletedNotifications.php
│   ├── SendContractExpirationNotifications.php
│   └── SendBirthdayReminders.php
│
├── Document/
│   ├── SendDocumentUploadedNotification.php
│   ├── SendDocumentExpirationNotifications.php
│   └── SendDocumentStatusNotifications.php
│
├── Salary/
│   ├── SendSalaryChangeNotifications.php
│   └── SendSalaryReviewReminders.php
│
├── Project/
│   ├── SendProjectAssignmentNotifications.php
│   └── SendProjectDeadlineNotifications.php
│
└── ... (uno por cada módulo)
```

### Capa 3: Notifications (Clases de Notificación)
```
App/Notifications/
├── Employee/
│   ├── EmployeeCreatedNotification.php
│   ├── EmployeeDeletedNotification.php
│   ├── ContractExpiringNotification.php
│   ├── BirthdayReminderNotification.php
│   └── AnniversaryReminderNotification.php
│
├── Document/
│   ├── DocumentUploadedNotification.php
│   ├── DocumentApprovedNotification.php
│   ├── DocumentRejectedNotification.php
│   └── DocumentExpiringNotification.php
│
├── Salary/
│   ├── SalaryCreatedNotification.php
│   ├── SalaryUpdatedNotification.php
│   └── SalaryReviewUpcomingNotification.php
│
├── Project/
│   ├── ProjectAssignmentNotification.php
│   ├── ProjectRemovalNotification.php
│   └── ProjectDeadlineNotification.php
│
├── Auth/
│   ├── RoleAssignedNotification.php
│   ├── RoleRemovedNotification.php
│   └── LoginFromNewLocationNotification.php
│
└── System/
    ├── BulkImportNotification.php
    ├── ExportReadyNotification.php
    └── BackupStatusNotification.php
```

### Capa 4: Channels (Canales de Envío)
```
App/Notifications/Channels/
├── DatabaseChannel.php (Laravel nativo)
├── MailChannel.php (Laravel nativo)
├── SmsChannel.php (Custom - Twilio/Nexmo)
└── WhatsAppChannel.php (Custom - Twilio WhatsApp API)
```

### Capa 5: Services (Lógica de Negocio)
```
App/Services/Notifications/
├── NotificationService.php → Orquestador principal
├── NotificationPreferenceService.php → Gestión de preferencias
├── NotificationDigestService.php → Agrupación de notificaciones
├── NotificationTemplateService.php → Gestión de plantillas
├── NotificationLogService.php → Auditoría
└── NotificationChannelRouter.php → Decide qué canal usar
```

---

## 🔧 Componentes Auxiliares

### Jobs (Trabajos en Cola)
```
App/Jobs/Notifications/
├── SendBulkNotifications.php
├── ProcessNotificationDigest.php
├── CheckDocumentExpirations.php
├── CheckContractExpirations.php
├── SendBirthdayReminders.php
└── CleanOldNotifications.php
```

### Commands (Comandos Artisan)
```
App/Console/Commands/Notifications/
├── SendDailyDigest.php
├── SendWeeklyDigest.php
├── CheckExpirations.php
├── CleanOldNotifications.php
└── TestNotification.php
```

### Middleware
```
App/Http/Middleware/
└── TrackNotificationRead.php
```

---

## 🎨 Componentes Frontend (Svelte)

### Componentes de UI
```
resources/js/Components/Notifications/
├── NotificationBell.svelte → Campana con contador
├── NotificationDropdown.svelte → Desplegable
├── NotificationItem.svelte → Item individual
├── NotificationList.svelte → Lista completa
├── NotificationCenter.svelte → Página completa
├── NotificationPreferences.svelte → Configuración
├── NotificationFilters.svelte → Filtros
└── NotificationEmpty.svelte → Estado vacío
```

### Stores (Estado Global)
```
resources/js/stores/notificationStore.js
├── notifications (array)
├── unreadCount (number)
├── fetchNotifications()
├── markAsRead(id)
├── markAllAsRead()
├── deleteNotification(id)
└── subscribeToUpdates() (WebSockets/Pusher)
```

---

## 🛣️ Rutas API Necesarias

### API Routes (api.php)
```php
Route::prefix('notifications')->group(function () {
    Route::get('/', 'NotificationController@index');
    Route::get('/unread-count', 'NotificationController@unreadCount');
    Route::post('/{id}/read', 'NotificationController@markAsRead');
    Route::post('/read-all', 'NotificationController@markAllAsRead');
    Route::delete('/{id}', 'NotificationController@destroy');
    Route::get('/preferences', 'NotificationController@getPreferences');
    Route::put('/preferences', 'NotificationController@updatePreferences');
    Route::get('/types', 'NotificationController@getTypes');
});
```

### Web Routes (web.php)
```php
Route::get('/notifications', 'NotificationController@page')
    ->name('notifications.page');
```

---

## ⚙️ Archivo de Configuración

### config/notifications.php
```php
return [
    // Canales disponibles
    'channels' => ['database', 'mail', 'sms', 'whatsapp'],
    
    // Prioridad por defecto
    'default_priority' => 'informative',
    
    // Resúmenes
    'digest_enabled' => true,
    'digest_default_frequency' => 'daily',
    'digest_time' => '09:00',
    
    // Retención
    'retention_days' => 90,
    
    // Batch
    'batch_size' => 100,
    
    // Proveedores
    'sms_provider' => 'twilio', // 'twilio' | 'nexmo'
    'whatsapp_enabled' => false,
    
    // Críticas siempre inmediatas
    'critical_always_immediate' => true,
    
    // Rate limits (por hora)
    'rate_limits' => [
        'email' => 50,
        'sms' => 10,
    ],
];
```

---

## 📋 Flujo de Trabajo Típico

### Ejemplo: Usuario crea un empleado

**1. Controller** → `EmployeeController@store()`
```
- Crea el empleado en BD
- Dispara: event(new EmployeeCreated($employee))
```

**2. Event** → `EmployeeCreated`
```
- Contiene: $employee, $createdBy, $timestamp
```

**3. Listener** → `SendEmployeeCreatedNotifications`
```
- Determina receptores:
  * Admin de RH → SÍ
  * Gerente del departamento → SÍ
  * Usuario que creó → NO
- Consulta preferencias de cada usuario
- Consulta NotificationTemplateService
- Consulta NotificationChannelRouter
- Encola Notification::send()
```

**4. Notification** → `EmployeeCreatedNotification`
```
- Implementa: ShouldQueue
- Define métodos:
  * via($notifiable) → ['database', 'mail']
  * toDatabase($notifiable)
  * toMail($notifiable)
  * toArray($notifiable)
```

**5. Channels**
```
- DatabaseChannel: Guarda en tabla notifications
- MailChannel: Envía email
- SmsChannel: (Si crítico) Envía SMS
```

**6. Log** → `NotificationLogService`
```
- Registra en notification_logs cada envío
```

**7. Frontend**
```
- WebSocket/Pusher: Push en tiempo real
- O polling cada 30 seg
- Actualiza contador campana
- Muestra toast
```

---

## 🎨 Diseño UI/UX

### Header con Campana
```
┌─────────────────────────────────────────┐
│ [Logo] [Menú] ... [🔔 (12)] [👤Avatar] │
└─────────────────────────────────────────┘
                      ↓ Click
         ┌────────────────────────────┐
         │ 🔔 Notificaciones          │
         ├────────────────────────────┤
         │ [🔴] Contrato vence hoy    │
         │      Juan Pérez            │
         │      Hace 2 horas   [...]  │
         ├────────────────────────────┤
         │ [🟠] Nuevo empleado        │
         │      María González        │
         │      Hace 3 horas   [...]  │
         ├────────────────────────────┤
         │ [🟡] Documento aprobado    │
         │      Certificación ISO     │
         │      Hace 1 día      [...]  │
         ├────────────────────────────┤
         │ [Ver todas] [Marcar leídas]│
         └────────────────────────────┘
```

### Centro de Notificaciones (Página)
```
┌─────────────────────────────────────────────────────┐
│ 🔔 Centro de Notificaciones                         │
├─────────────────────────────────────────────────────┤
│ [🔍 Buscar...] [Filtros▼] [Marcar todas como leídas]│
├─────────────────────────────────────────────────────┤
│ [Todas] [Críticas🔴] [Importantes🟠] [No leídas]    │
├─────────────────────────────────────────────────────┤
│                                                     │
│ HOY                                                 │
│ ├── [🔴] Contrato vence en 3 días                  │
│ │        Juan Pérez - Ventas                       │
│ │        Hace 2 horas                [Ver detalle] │
│ │                                                   │
│ └── [🟠] Nuevo empleado agregado                   │
│          María González                            │
│          Hace 5 horas                 [Ver perfil] │
│                                                     │
│ AYER                                                │
│ └── [🟡] Documento aprobado                        │
│          Certificación ISO                         │
│          Hace 1 día              [Ver documento]   │
└─────────────────────────────────────────────────────┘
```

### Configuración de Preferencias
```
┌─────────────────────────────────────────────────────┐
│ ⚙️ Preferencias de Notificaciones                   │
├─────────────────────────────────────────────────────┤
│                                                     │
│ 📧 Canales de Notificación                          │
│ ├── [✓] Notificaciones en dashboard                │
│ ├── [✓] Correo electrónico                         │
│ └── [✓] SMS (solo críticas)                        │
│                                                     │
│ 📅 Frecuencia de Resúmenes                          │
│ ├── ( ) Inmediato                                  │
│ ├── ( ) Cada hora                                  │
│ ├── (•) Diario a las [09:00▼]                     │
│ └── ( ) Semanal los lunes                          │
│                                                     │
│ 🔔 Tipos de Notificaciones                          │
│                                                     │
│ [Empleados]                                         │
│ ├── [✓] Nuevo empleado     [📧] [📱]               │
│ ├── [✓] Baja de empleado   [📧] [📱] [📞]          │
│ ├── [✓] Vence contrato     [📧] [📱] [📞]          │
│ └── [ ] Cumpleaños         [📱]                     │
│                                                     │
│ [Documentos]                                        │
│ ├── [✓] Vencido            [📧] [📱] [📞]          │
│ ├── [✓] Por vencer         [📧] [📱]               │
│ └── [✓] Aprobado           [📱]                     │
│                                                     │
│ 🔕 No Molestar                                      │
│ └── [✓] Desde [22:00▼] hasta [07:00▼]             │
│                                                     │
│ [Guardar Preferencias]                              │
└─────────────────────────────────────────────────────┘
```

---

## 🔐 Nuevos Permisos a Agregar

```php
// Agregar al PermissionsSeeder.php

// Notificaciones
['name' => 'notifications.view', 'description' => 'Ver notificaciones propias', 'module' => 'Notificaciones'],
['name' => 'notifications.view-all', 'description' => 'Ver todas las notificaciones', 'module' => 'Notificaciones'],
['name' => 'notifications.mark-read', 'description' => 'Marcar como leídas', 'module' => 'Notificaciones'],
['name' => 'notifications.delete', 'description' => 'Eliminar notificaciones', 'module' => 'Notificaciones'],
['name' => 'notifications.preferences', 'description' => 'Configurar preferencias', 'module' => 'Notificaciones'],
['name' => 'notifications.send-manual', 'description' => 'Enviar notificaciones manuales', 'module' => 'Notificaciones'],
['name' => 'notifications.manage-templates', 'description' => 'Gestionar plantillas', 'module' => 'Notificaciones'],
['name' => 'notifications.view-analytics', 'description' => 'Ver analytics', 'module' => 'Notificaciones'],
```

---

## 📦 Paquetes Recomendados

### Notificaciones
- `laravel/slack-notification-channel` (si usan Slack)

### SMS/WhatsApp
- `twilio/sdk` ⭐ **RECOMENDADO**
- O `laravel-notification-channels/twilio`

### Push en Tiempo Real
- `pusher/pusher-php-server` + Pusher.js
- O `beyondcode/laravel-websockets` (gratis, self-hosted)

### Email
- SMTP nativo de Laravel
- O servicios: SendGrid, Mailgun, Amazon SES

---

## 🚀 Fases de Implementación

### ✅ Fase 1: Core (2-3 semanas)
- Migraciones de BD
- Modelos y relaciones
- Sistema de eventos básico
- Notificaciones en Dashboard
- Componente de campana funcional

### ✅ Fase 2: Email (1 semana)
- Configuración de email
- Plantillas de email
- Sistema de colas
- Envío de emails

### ✅ Fase 3: Preferencias (1 semana)
- UI de configuración
- Lógica de preferencias
- Respeto a preferencias en envíos

### ✅ Fase 4: SMS/WhatsApp (1 semana)
- Integración con Twilio
- Solo para críticas
- Rate limiting

### ✅ Fase 5: Analytics (1 semana)
- Dashboard de métricas
- Logs y auditoría
- Reportes

### ✅ Fase 6: Tiempo Real (Opcional)
- WebSockets/Pusher
- Notificaciones push
- Actualizaciones en vivo

---

## 📊 Métricas a Trackear

1. **Tasa de apertura de emails** (objetivo: >70%)
2. **Tiempo de respuesta a críticas** (objetivo: <30 min)
3. **Notificaciones desactivadas** (identificar spam)
4. **No leídas por usuario** (sobrecarga)
5. **Efectividad de canal** (más acciones)

---

## ⚙️ Configuración de Horarios

### Frecuencia de Envío
- **Inmediatas**: Críticas (🔴)
- **Agrupadas cada hora**: Importantes (🟠)
- **Resumen diario**: Informativas (🟡)
- **Resumen semanal**: Opcionales (⚪)

### Horarios
- **SMS/WhatsApp**: Solo 8am - 6pm
- **Emails críticos**: 24/7
- **Emails informativos**: Horario laboral
- **Dashboard**: 24/7

---

## ✅ Checklist de Implementación

### Antes de empezar
- [ ] Decidir proveedor SMS (Twilio)
- [ ] Decidir Pusher vs Laravel WebSockets
- [ ] Configurar servicio email
- [ ] Definir plantillas visuales

### Durante desarrollo
- [ ] Crear migraciones
- [ ] Crear seeders para templates
- [ ] Implementar eventos
- [ ] Implementar listeners
- [ ] Crear clases de notificación
- [ ] Implementar canales custom
- [ ] Crear componentes Svelte
- [ ] Crear API routes
- [ ] Implementar preferencias
- [ ] Implementar jobs periódicos
- [ ] Implementar commands
- [ ] Testing

### Después de implementar
- [ ] Documentar tipos
- [ ] Crear guía de usuario
- [ ] Configurar cron jobs
- [ ] Monitorear métricas
- [ ] Ajustar según feedback

---

## 📝 Roles Actuales del Sistema

```php
1. admin → Administrador del sistema
2. hr → Recursos humanos
3. manager → Jefe de departamento o proyecto
4. employee → Empleado regular (no usará el sistema por ahora)
```

---

## 🎯 Configuración por Rol (Notificaciones)

### Super Admin
- Recibe: TODO
- Canales: Email + Dashboard + SMS (críticas)

### HR (Recursos Humanos)
- Recibe: Empleados, Documentos, Salarios, Contactos
- Canales: Email + Dashboard

### Manager (Gerente)
- Recibe: Solo de su departamento
- Canales: Email + Dashboard

### Employee
- Por ahora: NO APLICA

---

## 📧 Plantillas de Email

### Estructura Recomendada
```html
[HEADER con logo y branding]
[PRIORIDAD badge: 🔴 CRÍTICO / 🟠 IMPORTANTE / 🟡 INFO]
[TÍTULO de la notificación]
[MENSAJE principal]
[DATOS relevantes en tabla]
[BOTÓN de acción (si aplica)]
[FOOTER con info de contacto]
[LINK para desactivar notificaciones]
```

---

## 🔄 Comandos Cron a Configurar

```bash
# Resumen diario a las 9:00 AM
0 9 * * * php artisan notifications:digest daily

# Resumen semanal los lunes a las 9:00 AM
0 9 * * 1 php artisan notifications:digest weekly

# Verificar vencimientos cada 6 horas
0 */6 * * * php artisan notifications:check-expirations

# Enviar recordatorios de cumpleaños a las 8:00 AM
0 8 * * * php artisan notifications:birthday-reminders

# Limpiar notificaciones antiguas diariamente
0 2 * * * php artisan notifications:clean --days=90
```

---

## 📱 Integración con Twilio (SMS/WhatsApp)

### Variables de Entorno Necesarias
```env
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=+1234567890
TWILIO_WHATSAPP_NUMBER=whatsapp:+1234567890
```

### Limitaciones SMS
- Máximo 160 caracteres
- Solo notificaciones críticas
- Rate limit: 10 por hora por usuario
- Solo en horario laboral

---

## 🎨 Paleta de Colores para Prioridades

```css
:root {
  --critical: #dc3545;    /* Rojo Bootstrap danger */
  --important: #fd7e14;   /* Naranja Bootstrap warning */
  --informative: #0d6efd; /* Azul Bootstrap primary */
  --optional: #6c757d;    /* Gris Bootstrap secondary */
}
```

---

## 📚 Documentación de Referencia

### Laravel
- [Laravel Notifications](https://laravel.com/docs/11.x/notifications)
- [Laravel Events](https://laravel.com/docs/11.x/events)
- [Laravel Queues](https://laravel.com/docs/11.x/queues)

### Twilio
- [Twilio SMS](https://www.twilio.com/docs/sms)
- [Twilio WhatsApp](https://www.twilio.com/docs/whatsapp)

### Pusher
- [Pusher Laravel](https://pusher.com/docs/channels/getting_started/laravel)

---

## 🐛 Testing Recomendado

### Unit Tests
```php
- NotificationServiceTest
- NotificationPreferenceServiceTest
- NotificationChannelRouterTest
- NotificationTemplateServiceTest
```

### Feature Tests
```php
- SendEmployeeCreatedNotificationTest
- SendDocumentExpiringNotificationTest
- SendSalaryUpdatedNotificationTest
- NotificationPreferencesTest
- NotificationAPITest
```

### Browser Tests (Dusk)
```php
- NotificationBellTest
- NotificationCenterTest
- NotificationPreferencesTest
```

---

## 🔒 Seguridad

### Consideraciones
- ✅ Validar permisos antes de enviar
- ✅ No enviar info sensible por SMS
- ✅ Encriptar datos sensibles en BD
- ✅ Rate limiting en todas las APIs
- ✅ Sanitizar inputs en plantillas
- ✅ Logs de auditoría completos
- ✅ HTTPS obligatorio para webhooks

---

## 🎯 KPIs del Sistema

### Métricas de Éxito
- Tasa de apertura emails: >70%
- Tiempo respuesta críticas: <30min
- Satisfacción usuarios: >4/5
- Notificaciones fallidas: <1%
- Tiempo promedio lectura: <2h

---

## 📞 Soporte y Contacto

Para dudas o problemas con el sistema de notificaciones:
- Revisar logs en `storage/logs/laravel.log`
- Revisar tabla `notification_logs`
- Contactar al equipo de desarrollo

---

## 🔄 Historial de Versiones

### v1.0 - 27 Oct 2025
- Diseño inicial del sistema
- Definición de arquitectura
- Matriz de notificaciones
- Plan de implementación

---

## 📝 Notas Finales

- Este documento debe actualizarse conforme avanza la implementación
- Guardar en el repositorio en `/docs/NOTIFICATIONS_PLAN.md`
- Revisar y ajustar según feedback de usuarios
- Considerar agregar más canales en el futuro (Slack, Teams, etc)

---

**¡Sistema listo para implementación! 🚀**

---

## 🔗 Enlaces Útiles

- Repositorio: [URL_DEL_REPOSITORIO]
- Documentación Laravel: https://laravel.com/docs
- Documentación Twilio: https://www.twilio.com/docs
- Documentación Pusher: https://pusher.com/docs

---

_Documento creado: 27 de Octubre, 2025_  
_Autor: Equipo de Desarrollo AppLSI_  
_Versión: 1.0_