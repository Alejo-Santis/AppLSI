<?php

namespace App\Events\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    public User $deletedBy;

    public ?string $reason;

    public function __construct(Employee $employee, User $deletedBy, ?string $reason = null)
    {
        $this->employee = $employee;
        $this->deletedBy = $deletedBy;
        $this->reason = $reason;
    }

    public function getNotificationData(): array
    {
        return [
            'employee_name' => $this->employee->full_name ?? $this->employee->name,
            'department' => $this->employee->department->name ?? 'N/A',
            'termination_date' => now()->format('d/m/Y'),
            'reason' => $this->reason ?? 'No especificado',
            'deleted_by' => $this->deletedBy->name,
        ];
    }
}
