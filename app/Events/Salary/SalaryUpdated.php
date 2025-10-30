<?php

namespace App\Events\Salary;

use App\Models\Employee;
use App\Models\SalaryHistory;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalaryUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;

    public SalaryHistory $salaryHistory;

    public User $updatedBy;

    public ?float $oldSalary;

    public function __construct(
        Employee $employee,
        SalaryHistory $salaryHistory,
        User $updatedBy,
        ?float $oldSalary = null
    ) {
        $this->employee = $employee;
        $this->salaryHistory = $salaryHistory;
        $this->updatedBy = $updatedBy;
        $this->oldSalary = $oldSalary;
    }

    public function getNotificationData(): array
    {
        $data = [
            'employee_name' => $this->employee->full_name ?? $this->employee->name,
            'new_salary' => number_format($this->salaryHistory->salary, 2),
            'salary_amount' => number_format($this->salaryHistory->salary, 2),
            'effective_date' => $this->salaryHistory->effective_date?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'updated_by' => $this->updatedBy->name,
            'created_by' => $this->updatedBy->name, // Alias
        ];

        if ($this->oldSalary !== null) {
            $difference = $this->salaryHistory->salary - $this->oldSalary;
            $data['old_salary'] = number_format($this->oldSalary, 2);
            $data['difference'] = ($difference >= 0 ? '+' : '').number_format($difference, 2);
        }

        return $data;
    }

    public function getNotificationType(): string
    {
        return $this->oldSalary !== null ? 'salary_updated' : 'salary_created';
    }
}
