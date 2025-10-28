<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notification_type',
        'subject_email',
        'body_email',
        'body_sms',
        'body_whatsapp',
        'priority',
        'default_channels',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_channels' => 'array',
    ];

    /**
     * Priority levels.
     */
    public const PRIORITY_CRITICAL = 'critical';

    public const PRIORITY_IMPORTANT = 'important';

    public const PRIORITY_INFORMATIVE = 'informative';

    public const PRIORITY_OPTIONAL = 'optional';

    /**
     * Scope a query to only include templates of a specific priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include critical templates.
     */
    public function scopeCritical($query)
    {
        return $query->where('priority', self::PRIORITY_CRITICAL);
    }

    /**
     * Scope a query to only include important templates.
     */
    public function scopeImportant($query)
    {
        return $query->where('priority', self::PRIORITY_IMPORTANT);
    }

    /**
     * Check if this template is critical.
     */
    public function isCritical(): bool
    {
        return $this->priority === self::PRIORITY_CRITICAL;
    }

    /**
     * Check if this template is important.
     */
    public function isImportant(): bool
    {
        return $this->priority === self::PRIORITY_IMPORTANT;
    }

    /**
     * Check if this template is informative.
     */
    public function isInformative(): bool
    {
        return $this->priority === self::PRIORITY_INFORMATIVE;
    }

    /**
     * Check if this template is optional.
     */
    public function isOptional(): bool
    {
        return $this->priority === self::PRIORITY_OPTIONAL;
    }

    /**
     * Replace variables in a template string.
     */
    public function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{'.$key.'}}', $value ?? '', $template);
        }

        return $template;
    }

    /**
     * Get the email subject with replaced variables.
     */
    public function getEmailSubject(array $variables = []): string
    {
        return $this->replaceVariables($this->subject_email ?? '', $variables);
    }

    /**
     * Get the email body with replaced variables.
     */
    public function getEmailBody(array $variables = []): string
    {
        return $this->replaceVariables($this->body_email ?? '', $variables);
    }

    /**
     * Get the SMS body with replaced variables.
     */
    public function getSmsBody(array $variables = []): string
    {
        $body = $this->replaceVariables($this->body_sms ?? '', $variables);

        // Truncate to 160 characters for SMS
        return mb_substr($body, 0, 160);
    }

    /**
     * Get the WhatsApp body with replaced variables.
     */
    public function getWhatsAppBody(array $variables = []): string
    {
        return $this->replaceVariables($this->body_whatsapp ?? '', $variables);
    }

    /**
     * Check if a specific channel is in the default channels.
     */
    public function hasDefaultChannel(string $channel): bool
    {
        return in_array($channel, $this->default_channels ?? []);
    }

    /**
     * Get the default channels for this template.
     */
    public function getDefaultChannels(): array
    {
        return $this->default_channels ?? ['database'];
    }

    /**
     * Find a template by notification type.
     */
    public static function findByType(string $type): ?self
    {
        return static::where('notification_type', $type)->first();
    }

    /**
     * Get all templates grouped by priority.
     */
    public static function groupedByPriority(): array
    {
        return [
            self::PRIORITY_CRITICAL => static::critical()->get(),
            self::PRIORITY_IMPORTANT => static::important()->get(),
            self::PRIORITY_INFORMATIVE => static::byPriority(self::PRIORITY_INFORMATIVE)->get(),
            self::PRIORITY_OPTIONAL => static::byPriority(self::PRIORITY_OPTIONAL)->get(),
        ];
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            self::PRIORITY_CRITICAL => 'danger',
            self::PRIORITY_IMPORTANT => 'warning',
            self::PRIORITY_INFORMATIVE => 'primary',
            self::PRIORITY_OPTIONAL => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get priority icon for UI.
     */
    public function getPriorityIcon(): string
    {
        return match ($this->priority) {
            self::PRIORITY_CRITICAL => '🔴',
            self::PRIORITY_IMPORTANT => '🟠',
            self::PRIORITY_INFORMATIVE => '🟡',
            self::PRIORITY_OPTIONAL => '⚪',
            default => '⚪',
        };
    }
}
