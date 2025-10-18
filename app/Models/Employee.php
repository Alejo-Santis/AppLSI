<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'salary',
        'status',
        'birth_date',
        'address',
        'photo_url',
        'position_id',
        'department_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'birth_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    /**
     * Appends attributes
     */
    protected $appends = ['full_name', 'years_of_service'];

    /**
     * Relación: Departamento
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relación: Puesto/Posición
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relación: Usuario del sistema
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relación: Documentos
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relación: Contactos de emergencia
     */
    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    /**
     * Relación: Historial salarial
     */
    public function salaryHistories()
    {
        return $this->hasMany(SalaryHistory::class);
    }

    /**
     * Relación: Asignaciones de proyectos
     */
    public function projectAssignments()
    {
        return $this->hasMany(ProjectAssignments::class);
    }

    /**
     * Relación: Proyectos (a través de asignaciones)
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_assignments')
            ->withPivot('role', 'assigned_date', 'end_date', 'allocation_percentage', 'is_active')
            ->withTimestamps();
    }

    /**
     * Relación: Departamentos donde es manager
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    /**
     * Relación: Proyectos donde es manager
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Scope: Solo empleados activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Buscar empleados
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Por departamento
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope: Por puesto
     */
    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /**
     * Scope: Por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessor: Nombre completo
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Accessor: Años de servicio
     */
    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date->diffInYears(now());
    }

    /**
     * Accessor: Edad
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->diffInYears(now()) : null;
    }

    /**
     * Método: Verificar si se puede eliminar
     */
    public function canBeDeleted(): bool
    {
        return !$this->projectAssignments()->where('is_active', true)->exists()
            && !$this->managedDepartments()->exists()
            && !$this->managedProjects()->exists();
    }

    /**
     * Método: Obtener proyectos activos
     */
    public function getActiveProjects()
    {
        return $this->projectAssignments()
            ->where('is_active', true)
            ->with('project')
            ->get();
    }

    /**
     * Método: Calcular porcentaje de asignación total
     */
    public function getTotalAllocationPercentage()
    {
        return $this->projectAssignments()
            ->where('is_active', true)
            ->sum('allocation_percentage');
    }
}
