<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget',
        'project_manager_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    /**
     * Relación: Manager del proyecto
     */
    public function projectManager()
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    /**
     * Relación: Asignaciones de empleados
     */
    public function projectAssignments()
    {
        return $this->hasMany(ProjectAssignments::class);
    }

    /**
     * Relación: Empleados asignados (a través de assignments)
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_assignments')
            ->withPivot('role', 'assigned_date', 'end_date', 'allocation_percentage', 'is_active')
            ->withTimestamps();
    }

    /**
     * Scope: Proyectos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Buscar proyectos
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Días restantes
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date || $this->status === 'completed') {
            return 0;
        }

        $today = now()->startOfDay();
        $endDate = $this->end_date->startOfDay();

        return $today->diffInDays($endDate, false);
    }

    /**
     * Accessor: Porcentaje de progreso (basado en fechas)
     */
    public function getProgressPercentageAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $today = now()->startOfDay();
        $start = $this->start_date->startOfDay();
        $end = $this->end_date->startOfDay();

        if ($today < $start) {
            return 0;
        }

        if ($today >= $end) {
            return 100;
        }

        $totalDays = $start->diffInDays($end);
        $elapsedDays = $start->diffInDays($today);

        return round(($elapsedDays / $totalDays) * 100);
    }

    /**
     * Método: Estados disponibles
     */
    public static function getStatuses(): array
    {
        return [
            'planning' => 'En Planeación',
            'active' => 'Activo',
            'on_hold' => 'En Pausa',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
        ];
    }

    /**
     * Método: Generar código único
     */
    public static function generateCode(): string
    {
        $lastProject = self::orderBy('id', 'desc')->first();
        $number = $lastProject ? (int) substr($lastProject->code, 4) + 1 : 1;
        return 'PROJ' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Método: Obtener empleados activos del proyecto
     */
    public function getActiveEmployees()
    {
        return $this->projectAssignments()
            ->where('is_active', true)
            ->with('employee')
            ->get();
    }
}
