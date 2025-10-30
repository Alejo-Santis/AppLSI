<?php

namespace App\Listeners\Employee;

use App\Events\Employee\ContractExpiring;
use App\Services\Notifications\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendContractExpiringNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ContractExpiring $event): void
    {
        $data = $event->getNotificationData();
        $notificationType = $event->getNotificationType();

        // Enviar a roles configurados (HR y Admin)
        $this->notificationService->sendByRoles(
            notificationType: $notificationType,
            data: $data
        );

        // Notificar al gerente directo
        if ($event->employee->department && $event->employee->department->manager) {
            $this->notificationService->sendToUser(
                user: $event->employee->department->manager->user,
                notificationType: $notificationType,
                data: $data
            );
        }
    }

    public function failed(ContractExpiring $event, \Throwable $exception): void
    {
        Log::error('Failed to send contract expiring notifications', [
            'employee_id' => $event->employee->id,
            'expiration_date' => $event->expirationDate,
            'error' => $exception->getMessage(),
        ]);
    }
}