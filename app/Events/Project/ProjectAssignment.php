<?php

namespace App\Events\Project;

use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectAssignment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project $project;

    public Employee $employee;

    public User $assignedBy;

    public ?string $role;

    public bool $isAssignment; // true = assigned, false = removed

    public function __construct(
        Project $project,
        Employee $employee,
        User $assignedBy,
        bool $isAssignment = true,
        ?string $role = null
    ) {
        $this->project = $project;
        $this->employee = $employee;
        $this->assignedBy = $assignedBy;
        $this->isAssignment = $isAssignment;
        $this->role = $role;
    }

    public function getNotificationData(): array
    {
        $data = [
            'project_name' => $this->project->name,
            'employee_name' => $this->employee->full_name ?? $this->employee->name,
            'project_manager' => $this->project->manager->name ?? 'N/A',
            'start_date' => $this->project->start_date?->format('d/m/Y') ?? 'N/A',
        ];

        if ($this->isAssignment) {
            $data['role'] = $this->role ?? 'Miembro del equipo';
        } else {
            $data['removal_date'] = now()->format('d/m/Y');
            $data['reason'] = 'Reasignación de recursos';
        }

        return $data;
    }

    public function getNotificationType(): string
    {
        return $this->isAssignment
            ? 'employee_assigned_to_project'
            : 'employee_removed_from_project';
    }
}
