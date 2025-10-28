<?php

namespace App\Traits;

use App\Models\NotificationDigestSetting;
use App\Models\NotificationLog;
use App\Models\NotificationPreference;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasNotificationPreferences
{
    /**
     * Get the user's notification preferences.
     */
    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Get the user's digest settings.
     */
    public function notificationDigestSetting(): HasOne
    {
        return $this->hasOne(NotificationDigestSetting::class);
    }

    /**
     * Get the user's notification logs.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Get or create digest settings for this user.
     */
    public function getDigestSettings(): NotificationDigestSetting
    {
        return NotificationDigestSetting::getOrCreateForUser($this->id);
    }

    /**
     * Get enabled channels for a specific notification type.
     */
    public function getEnabledChannelsForType(string $notificationType): array
    {
        $preference = $this->notificationPreferences()
            ->where('notification_type', $notificationType)
            ->first();

        if (! $preference) {
            // Return default channels from template
            $template = \App\Models\NotificationTemplate::findByType($notificationType);

            return $template ? $template->getDefaultChannels() : ['database'];
        }

        return $preference->getEnabledChannels();
    }

    /**
     * Check if a specific channel is enabled for a notification type.
     */
    public function isChannelEnabledForType(string $notificationType, string $channel): bool
    {
        $enabledChannels = $this->getEnabledChannelsForType($notificationType);

        return in_array($channel, $enabledChannels);
    }

    /**
     * Set preference for a notification type.
     */
    public function setNotificationPreference(
        string $notificationType,
        array $channels,
        bool $isEnabled = true
    ): NotificationPreference {
        return NotificationPreference::setPreference(
            $this->id,
            $notificationType,
            $channels,
            $isEnabled
        );
    }

    /**
     * Enable a notification type.
     */
    public function enableNotification(string $notificationType): void
    {
        $preference = $this->notificationPreferences()
            ->where('notification_type', $notificationType)
            ->first();

        if ($preference) {
            $preference->is_enabled = true;
            $preference->save();
        }
    }

    /**
     * Disable a notification type.
     */
    public function disableNotification(string $notificationType): void
    {
        $preference = $this->notificationPreferences()
            ->where('notification_type', $notificationType)
            ->first();

        if ($preference) {
            $preference->is_enabled = false;
            $preference->save();
        }
    }

    /**
     * Update digest settings.
     */
    public function updateDigestSettings(
        string $frequency,
        string $time = '09:00',
        bool $includeOptional = false
    ): NotificationDigestSetting {
        return NotificationDigestSetting::updateForUser(
            $this->id,
            $frequency,
            $time,
            $includeOptional
        );
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get notification statistics.
     */
    public function getNotificationStats(int $days = 30): array
    {
        return NotificationLog::getStatsForUser($this->id, $days);
    }

    /**
     * Check if user prefers immediate notifications.
     */
    public function prefersImmediateNotifications(): bool
    {
        $setting = $this->notificationDigestSetting;

        return $setting ? $setting->isImmediate() : true;
    }

    /**
     * Get recent notification logs.
     */
    public function getRecentNotificationLogs(int $days = 7)
    {
        return $this->notificationLogs()
            ->recent($days)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get failed notification logs.
     */
    public function getFailedNotificationLogs()
    {
        return $this->notificationLogs()
            ->failed()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
