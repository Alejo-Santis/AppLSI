<?php

namespace App\Events\Document;

use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentExpiring
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Document $document;

    public Carbon $expirationDate;

    public int $daysUntilExpiration;

    public function __construct(Document $document, Carbon $expirationDate)
    {
        $this->document = $document;
        $this->expirationDate = $expirationDate;
        $this->daysUntilExpiration = now()->diffInDays($expirationDate);
    }

    public function getNotificationData(): array
    {
        return [
            'document_name' => $this->document->name ?? $this->document->file_name,
            'employee_name' => $this->document->employee->full_name ?? $this->document->employee->name,
            'expiration_date' => $this->expirationDate->format('d/m/Y'),
            'days_remaining' => $this->daysUntilExpiration,
        ];
    }

    public function getNotificationType(): string
    {
        if ($this->daysUntilExpiration < 0) {
            return 'document_expired';
        } elseif ($this->daysUntilExpiration <= 7) {
            return 'document_expiring_7_days';
        } else {
            return 'document_expiring_30_days';
        }
    }
}
