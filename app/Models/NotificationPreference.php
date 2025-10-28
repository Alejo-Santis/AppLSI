<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'channel_email',
        'channel_database',
        'channel_sms',
        'is_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'channel_email' => 'boolean',
        'channel_database' => 'boolean',
        'channel_sms' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include preferences for a specific notification type.
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope a query to only include enabled preferences.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Check if a specific channel is enabled.
     */
    public function isChannelEnabled(string $channel): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        return match ($channel) {
            'email' => $this->channel_email,
            'database' => $this->channel_database,
            'sms' => $this->channel_sms,
            default => false,
        };
    }

    /**
     * Get all enabled channels for this preference.
     */
    public function getEnabledChannels(): array
    {
        if (! $this->is_enabled) {
            return [];
        }

        $channels = [];

        if ($this->channel_email) {
            $channels[] = 'email';
        }
        if ($this->channel_database) {
            $channels[] = 'database';
        }
        if ($this->channel_sms) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Enable a specific channel.
     */
    public function enableChannel(string $channel): void
    {
        match ($channel) {
            'email' => $this->channel_email = true,
            'database' => $this->channel_database = true,
            'sms' => $this->channel_sms = true,
            default => null,
        };
    }

    /**
     * Disable a specific channel.
     */
    public function disableChannel(string $channel): void
    {
        match ($channel) {
            'email' => $this->channel_email = false,
            'database' => $this->channel_database = false,
            'sms' => $this->channel_sms = false,
            default => null,
        };
    }

    /**
     * Create or update a preference for a user.
     */
    public static function setPreference(
        int $userId,
        string $notificationType,
        array $channels,
        bool $isEnabled = true
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'notification_type' => $notificationType,
            ],
            [
                'channel_email' => in_array('email', $channels),
                'channel_database' => in_array('database', $channels),
                'channel_sms' => in_array('sms', $channels),
                'is_enabled' => $isEnabled,
            ]
        );
    }
}
