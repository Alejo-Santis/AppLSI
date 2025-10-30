<?php

namespace App\Events\Employee;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractExpiring
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    public Carbon $expirationDate;

    public int $daysUntilExpiration;

    public function __construct(Employee $employee, Carbon $expirationDate)
    {
        $this->employee = $employee;
        $this->expirationDate = $expirationDate;
        $this->daysUntilExpiration = now()->diffInDays($expirationDate);
    }

    public function getNotificationData(): array
    {
        return [
            'employee_name' => $this->employee->full_name ?? $this->employee->name,
            'department' => $this->employee->department->name ?? 'N/A',
            'expiration_date' => $this->expirationDate->format('d/m/Y'),
            'days_remaining' => $this->daysUntilExpiration,
        ];
    }

    public function getNotificationType(): string
    {
        return $this->daysUntilExpiration <= 7
            ? 'contract_expiring_7_days'
            : 'contract_expiring_30_days';
    }
}
