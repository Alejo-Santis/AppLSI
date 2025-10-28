<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationDigestSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'digest_frequency',
        'digest_time',
        'include_optional',
        'last_digest_sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'digest_time' => 'datetime:H:i',
        'include_optional' => 'boolean',
        'last_digest_sent_at' => 'datetime',
    ];

    /**
     * Frequency constants.
     */
    public const FREQUENCY_IMMEDIATE = 'immediate';

    public const FREQUENCY_HOURLY = 'hourly';

    public const FREQUENCY_DAILY = 'daily';

    public const FREQUENCY_WEEKLY = 'weekly';

    /**
     * Get the user that owns the setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include settings by frequency.
     */
    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('digest_frequency', $frequency);
    }

    /**
     * Scope a query to only include immediate settings.
     */
    public function scopeImmediate($query)
    {
        return $query->where('digest_frequency', self::FREQUENCY_IMMEDIATE);
    }

    /**
     * Scope a query to only include hourly settings.
     */
    public function scopeHourly($query)
    {
        return $query->where('digest_frequency', self::FREQUENCY_HOURLY);
    }

    /**
     * Scope a query to only include daily settings.
     */
    public function scopeDaily($query)
    {
        return $query->where('digest_frequency', self::FREQUENCY_DAILY);
    }

    /**
     * Scope a query to only include weekly settings.
     */
    public function scopeWeekly($query)
    {
        return $query->where('digest_frequency', self::FREQUENCY_WEEKLY);
    }

    /**
     * Scope a query to only include settings that are due for a digest.
     */
    public function scopeDueForDigest($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('last_digest_sent_at')
                ->orWhere('last_digest_sent_at', '<=', now()->subHour());
        });
    }

    /**
     * Check if user prefers immediate notifications.
     */
    public function isImmediate(): bool
    {
        return $this->digest_frequency === self::FREQUENCY_IMMEDIATE;
    }

    /**
     * Check if user prefers hourly digest.
     */
    public function isHourly(): bool
    {
        return $this->digest_frequency === self::FREQUENCY_HOURLY;
    }

    /**
     * Check if user prefers daily digest.
     */
    public function isDaily(): bool
    {
        return $this->digest_frequency === self::FREQUENCY_DAILY;
    }

    /**
     * Check if user prefers weekly digest.
     */
    public function isWeekly(): bool
    {
        return $this->digest_frequency === self::FREQUENCY_WEEKLY;
    }

    /**
     * Check if digest is due to be sent.
     */
    public function isDue(): bool
    {
        if ($this->isImmediate()) {
            return true;
        }

        if ($this->last_digest_sent_at === null) {
            return true;
        }

        return match ($this->digest_frequency) {
            self::FREQUENCY_HOURLY => $this->last_digest_sent_at->addHour()->isPast(),
            self::FREQUENCY_DAILY => $this->last_digest_sent_at->addDay()->isPast()
                && now()->format('H:i') >= $this->digest_time->format('H:i'),
            self::FREQUENCY_WEEKLY => $this->last_digest_sent_at->addWeek()->isPast()
                && now()->format('H:i') >= $this->digest_time->format('H:i')
                && now()->isMonday(),
            default => true,
        };
    }

    /**
     * Mark the digest as sent.
     */
    public function markDigestAsSent(): void
    {
        $this->last_digest_sent_at = now();
        $this->save();
    }

    /**
     * Get or create settings for a user with defaults.
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'digest_frequency' => config('notifications.digest.default_frequency', self::FREQUENCY_DAILY),
                'digest_time' => config('notifications.digest.default_time', '09:00'),
                'include_optional' => config('notifications.digest.include_optional_by_default', false),
            ]
        );
    }

    /**
     * Update settings for a user.
     */
    public static function updateForUser(
        int $userId,
        string $frequency,
        string $time = '09:00',
        bool $includeOptional = false
    ): self {
        return static::updateOrCreate(
            ['user_id' => $userId],
            [
                'digest_frequency' => $frequency,
                'digest_time' => $time,
                'include_optional' => $includeOptional,
            ]
        );
    }

    /**
     * Get the next scheduled digest time.
     */
    public function getNextDigestTime(): ?\Carbon\Carbon
    {
        if ($this->isImmediate()) {
            return now();
        }

        $time = $this->digest_time;
        $today = now()->setTimeFromTimeString($time->format('H:i:s'));

        return match ($this->digest_frequency) {
            self::FREQUENCY_HOURLY => now()->addHour(),
            self::FREQUENCY_DAILY => $today->isFuture() ? $today : $today->addDay(),
            self::FREQUENCY_WEEKLY => $today->next('Monday')->setTimeFromTimeString($time->format('H:i:s')),
            default => null,
        };
    }

    /**
     * Get users due for hourly digest.
     */
    public static function getUsersDueForHourlyDigest()
    {
        return static::hourly()
            ->dueForDigest()
            ->with('user')
            ->get()
            ->pluck('user');
    }

    /**
     * Get users due for daily digest.
     */
    public static function getUsersDueForDailyDigest()
    {
        $currentTime = now()->format('H:i');

        return static::daily()
            ->whereTime('digest_time', '<=', $currentTime)
            ->where(function ($query) {
                $query->whereNull('last_digest_sent_at')
                    ->orWhereDate('last_digest_sent_at', '<', now()->toDateString());
            })
            ->with('user')
            ->get()
            ->pluck('user');
    }

    /**
     * Get users due for weekly digest.
     */
    public static function getUsersDueForWeeklyDigest()
    {
        if (! now()->isMonday()) {
            return collect();
        }

        $currentTime = now()->format('H:i');

        return static::weekly()
            ->whereTime('digest_time', '<=', $currentTime)
            ->where(function ($query) {
                $query->whereNull('last_digest_sent_at')
                    ->orWhere('last_digest_sent_at', '<=', now()->subWeek());
            })
            ->with('user')
            ->get()
            ->pluck('user');
    }
}
