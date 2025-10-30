<?php

namespace App\Listeners\Employee;

use App\Events\Employee\EmployeeCreated;
use App\Services\Notifications\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmployeeCreatedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The notification service instance.
     */
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeCreated $event): void
    {
        // Obtener los datos de la notificación
        $data = $event->getNotificationData();

        // Enviar notificación usando el servicio
        $this->notificationService->sendByRoles(
            notificationType: 'employee_created',
            data: $data,
            excludeUserId: $event->createdBy->id // No notificar al que creó
        );

        // También notificar al gerente del departamento si existe
        if ($event->employee->department && $event->employee->department->manager) {
            $this->notificationService->sendToUser(
                user: $event->employee->department->manager->user,
                notificationType: 'employee_created',
                data: $data
            );
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(EmployeeCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send employee created notifications', [
            'employee_id' => $event->employee->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
