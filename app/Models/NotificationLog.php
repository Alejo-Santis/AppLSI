<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notification_id',
        'user_id',
        'notification_type',
        'channel',
        'status',
        'sent_at',
        'read_at',
        'metadata',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Status constants.
     */
    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public const STATUS_PENDING = 'pending';

    /**
     * Channel constants.
     */
    public const CHANNEL_EMAIL = 'email';

    public const CHANNEL_DATABASE = 'database';

    public const CHANNEL_SMS = 'sms';

    public const CHANNEL_WHATSAPP = 'whatsapp';

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include logs of a specific status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include sent logs.
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope a query to only include failed logs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to only include pending logs.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include logs by channel.
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope a query to only include logs by notification type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope a query to only include read logs.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include unread logs.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if the log is sent.
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Check if the log failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the log is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the notification was read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->read_at = now();
            $this->save();
        }
    }

    /**
     * Mark the notification as sent.
     */
    public function markAsSent(): void
    {
        $this->status = self::STATUS_SENT;
        $this->sent_at = now();
        $this->save();
    }

    /**
     * Mark the notification as failed.
     */
    public function markAsFailed(?string $errorMessage = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->error_message = $errorMessage;
        $this->save();
    }

    /**
     * Create a log entry.
     */
    public static function createLog(
        ?string $notificationId,
        int $userId,
        string $notificationType,
        string $channel,
        string $status = self::STATUS_PENDING,
        array $metadata = []
    ): self {
        return static::create([
            'notification_id' => $notificationId,
            'user_id' => $userId,
            'notification_type' => $notificationType,
            'channel' => $channel,
            'status' => $status,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get statistics for a user.
     */
    public static function getStatsForUser(int $userId, int $days = 30): array
    {
        $logs = static::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total' => $logs->count(),
            'sent' => $logs->where('status', self::STATUS_SENT)->count(),
            'failed' => $logs->where('status', self::STATUS_FAILED)->count(),
            'pending' => $logs->where('status', self::STATUS_PENDING)->count(),
            'read' => $logs->whereNotNull('read_at')->count(),
            'unread' => $logs->whereNull('read_at')->count(),
            'by_channel' => [
                'email' => $logs->where('channel', self::CHANNEL_EMAIL)->count(),
                'database' => $logs->where('channel', self::CHANNEL_DATABASE)->count(),
                'sms' => $logs->where('channel', self::CHANNEL_SMS)->count(),
                'whatsapp' => $logs->where('channel', self::CHANNEL_WHATSAPP)->count(),
            ],
        ];
    }

    /**
     * Get global statistics.
     */
    public static function getGlobalStats(int $days = 30): array
    {
        $logs = static::where('created_at', '>=', now()->subDays($days))->get();

        $total = $logs->count();
        $sent = $logs->where('status', self::STATUS_SENT)->count();
        $read = $logs->whereNotNull('read_at')->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $logs->where('status', self::STATUS_FAILED)->count(),
            'pending' => $logs->where('status', self::STATUS_PENDING)->count(),
            'read' => $read,
            'unread' => $logs->whereNull('read_at')->count(),
            'read_rate' => $sent > 0 ? round(($read / $sent) * 100, 2) : 0,
            'by_channel' => [
                'email' => $logs->where('channel', self::CHANNEL_EMAIL)->count(),
                'database' => $logs->where('channel', self::CHANNEL_DATABASE)->count(),
                'sms' => $logs->where('channel', self::CHANNEL_SMS)->count(),
                'whatsapp' => $logs->where('channel', self::CHANNEL_WHATSAPP)->count(),
            ],
            'by_type' => $logs->groupBy('notification_type')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
        ];
    }
}
