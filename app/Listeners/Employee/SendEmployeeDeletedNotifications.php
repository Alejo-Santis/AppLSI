<?php

namespace App\Listeners\Employee;

use App\Events\Employee\EmployeeDeleted;
use App\Services\Notifications\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEmployeeDeletedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(EmployeeDeleted $event): void
    {
        $data = $event->getNotificationData();

        // Esta es CRÍTICA, enviar a roles específicos
        $this->notificationService->sendByRoles(
            notificationType: 'employee_deleted',
            data: $data,
            excludeUserId: $event->deletedBy->id
        );

        // Notificar al gerente del departamento
        if ($event->employee->department && $event->employee->department->manager) {
            $this->notificationService->sendToUser(
                user: $event->employee->department->manager->user,
                notificationType: 'employee_deleted',
                data: $data
            );
        }
    }

    public function failed(EmployeeDeleted $event, \Throwable $exception): void
    {
        Log::error('Failed to send employee deleted notifications', [
            'employee_id' => $event->employee->id,
            'error' => $exception->getMessage(),
        ]);
    }
}