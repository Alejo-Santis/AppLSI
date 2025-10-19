<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'previous_salary',
        'new_salary',
        'change_date',
        'reason',
        'approved_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'previous_salary' => 'decimal:2',
            'new_salary' => 'decimal:2',
            'change_date' => 'date',
        ];
    }

    /**
     * Relación: Empleado al que pertenece el historial
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relación: Usuario que aprobó el cambio
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Por empleado
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope: Por rango de fechas
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('change_date', [$startDate, $endDate]);
    }

    /**
     * Accessor: Diferencia de salario
     */
    public function getSalaryDifferenceAttribute()
    {
        return $this->new_salary - $this->previous_salary;
    }

    /**
     * Accessor: Porcentaje de cambio
     */
    public function getPercentageChangeAttribute()
    {
        if ($this->previous_salary == 0) {
            return 0;
        }
        return (($this->new_salary - $this->previous_salary) / $this->previous_salary) * 100;
    }

    /**
     * Accessor: Tipo de cambio (aumento/disminución)
     */
    public function getChangeTypeAttribute()
    {
        if ($this->new_salary > $this->previous_salary) {
            return 'increase';
        } elseif ($this->new_salary < $this->previous_salary) {
            return 'decrease';
        }
        return 'no_change';
    }

    /**
     * Método: Razones de cambio predefinidas
     */
    public static function getChangeReasons(): array
    {
        return [
            'promotion' => 'Promoción',
            'performance' => 'Desempeño Excepcional',
            'annual_adjustment' => 'Ajuste Anual',
            'inflation_adjustment' => 'Ajuste por Inflación',
            'position_change' => 'Cambio de Puesto',
            'market_adjustment' => 'Ajuste de Mercado',
            'demotion' => 'Degradación',
            'other' => 'Otro',
        ];
    }

    /**
     * Método: Crear registro de cambio salarial
     */
    public static function recordSalaryChange($employeeId, $previousSalary, $newSalary, $reason, $approvedBy)
    {
        return self::create([
            'employee_id' => $employeeId,
            'previous_salary' => $previousSalary,
            'new_salary' => $newSalary,
            'change_date' => now(),
            'reason' => $reason,
            'approved_by' => $approvedBy,
        ]);
    }
}
