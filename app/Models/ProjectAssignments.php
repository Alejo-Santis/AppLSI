<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssignments extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'project_id',
        'role',
        'assigned_date',
        'end_date',
        'allocation_percentage',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
            'end_date' => 'date',
            'allocation_percentage' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación: Empleado asignado
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación: Proyecto
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope: Solo asignaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Por empleado
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope: Por proyecto
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
