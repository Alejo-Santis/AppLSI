<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'level',
        'min_salary',
        'max_salary',
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
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación: Empleados con este puesto
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope: Solo puestos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Buscar por título o descripción
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('level', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Número de empleados
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Accessor: Empleados activos
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->employees()->where('status', 'active')->count();
    }

    /**
     * Accessor: Rango salarial formateado
     */
    public function getSalaryRangeAttribute()
    {
        if ($this->min_salary && $this->max_salary) {
            return '$' . number_format($this->min_salary, 0) . ' - $' . number_format($this->max_salary, 0);
        }
        return 'No definido';
    }

    /**
     * Método: Verificar si se puede eliminar
     */
    public function canBeDeleted(): bool
    {
        return $this->employees()->count() === 0;
    }

    /**
     * Método: Obtener niveles disponibles
     */
    public static function getLevels(): array
    {
        return [
            'junior' => 'Junior',
            'mid' => 'Mid-Level',
            'senior' => 'Senior',
            'lead' => 'Lead',
            'manager' => 'Manager',
            'director' => 'Director',
        ];
    }
}
