<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification to a specific user.
     */
    public function sendToUser(
        User $user,
        string $notificationType,
        array $data,
        ?array $forcedChannels = null
    ): void {
        // Obtener template
        $template = NotificationTemplate::findByType($notificationType);
        
        if (!$template) {
            Log::warning("Notification template not found: {$notificationType}");
            return;
        }

        // Obtener canales habilitados para este usuario
        $channels = $forcedChannels ?? $user->getEnabledChannelsForType($notificationType);

        if (empty($channels)) {
            Log::info("No channels enabled for user {$user->id} and type {$notificationType}");
            return;
        }

        // Las notificaciones críticas siempre deben enviarse inmediatamente
        // si el usuario tiene configurado digest
        if ($template->isCritical() && config('notifications.critical_always_immediate', true)) {
            $channels = $this->ensureImmediateDelivery($user, $channels);
        }

        try {
            // Enviar notificación
            $notification = new GenericNotification($template, $data, $channels);
            $user->notify($notification);

            // Registrar en logs
            foreach ($channels as $channel) {
                NotificationLog::createLog(
                    notificationId: null, // Se llenará después si es necesario
                    userId: $user->id,
                    notificationType: $notificationType,
                    channel: $channel,
                    status: NotificationLog::STATUS_SENT,
                    metadata: [
                        'sent_at' => now()->toIso8601String(),
                        'priority' => $template->priority,
                    ]
                )->markAsSent();
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification to user {$user->id}", [
                'type' => $notificationType,
                'error' => $e->getMessage(),
            ]);

            // Registrar fallo
            NotificationLog::createLog(
                notificationId: null,
                userId: $user->id,
                notificationType: $notificationType,
                channel: 'database',
                status: NotificationLog::STATUS_FAILED
            )->markAsFailed($e->getMessage());
        }
    }

    /**
     * Send notification to multiple users.
     */
    public function sendToUsers(
        Collection $users,
        string $notificationType,
        array $data
    ): void {
        foreach ($users as $user) {
            $this->sendToUser($user, $notificationType, $data);
        }
    }

    /**
     * Send notification to users with specific roles.
     */
    public function sendByRoles(
        string $notificationType,
        array $data,
        ?int $excludeUserId = null
    ): void {
        // Obtener roles configurados para este tipo de notificación
        $roles = config("notifications.role_notifications.{$notificationType}", []);

        if (empty($roles)) {
            Log::info("No roles configured for notification type: {$notificationType}");
            return;
        }

        // Obtener usuarios con esos roles
        $users = User::role($roles)->get();

        // Excluir usuario si se especifica
        if ($excludeUserId) {
            $users = $users->reject(fn($user) => $user->id === $excludeUserId);
        }

        $this->sendToUsers($users, $notificationType, $data);
    }

    /**
     * Send notification to users in a specific department.
     */
    public function sendToDepartment(
        int $departmentId,
        string $notificationType,
        array $data
    ): void {
        $users = User::whereHas('employee', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->get();

        $this->sendToUsers($users, $notificationType, $data);
    }

    /**
     * Ensure immediate delivery for critical notifications.
     */
    protected function ensureImmediateDelivery(User $user, array $channels): array
    {
        $digestSetting = $user->notificationDigestSetting;

        if (!$digestSetting || $digestSetting->isImmediate()) {
            return $channels;
        }

        // For non-immediate users with critical notifications,
        // ensure at least one immediate channel (database)
        if (!in_array('database', $channels)) {
            $channels[] = 'database';
        }

        return $channels;
    }

    /**
     * Get users who should receive a notification type.
     */
    public function getUsersForNotificationType(string $notificationType): Collection
    {
        $roles = config("notifications.role_notifications.{$notificationType}", []);

        if (empty($roles)) {
            return collect();
        }

        return User::role($roles)->get();
    }

    /**
     * Check if a user should receive a notification type.
     */
    public function shouldReceiveNotification(User $user, string $notificationType): bool
    {
        $channels = $user->getEnabledChannelsForType($notificationType);
        return !empty($channels);
    }
}