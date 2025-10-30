<?php

namespace App\Events\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The employee that was created.
     */
    public Employee $employee;

    /**
     * The user who created the employee.
     */
    public User $createdBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Employee $employee, User $createdBy)
    {
        $this->employee = $employee;
        $this->createdBy = $createdBy;
    }

    /**
     * Get the notification data.
     */
    public function getNotificationData(): array
    {
        return [
            'employee_name' => $this->employee->full_name ?? $this->employee->name,
            'department' => $this->employee->department->name ?? 'N/A',
            'position' => $this->employee->position->name ?? 'N/A',
            'hire_date' => $this->employee->hire_date?->format('d/m/Y') ?? 'N/A',
            'created_by' => $this->createdBy->name,
        ];
    }
}
