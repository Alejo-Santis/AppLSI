<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'relationship',
        'phone',
        'email',
        'address',
        'is_primary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Relación: Empleado al que pertenece el contacto
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope: Solo contactos primarios
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Por empleado
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Método: Tipos de relación disponibles
     */
    public static function getRelationshipTypes(): array
    {
        return [
            'spouse' => 'Esposo/a',
            'parent' => 'Padre/Madre',
            'sibling' => 'Hermano/a',
            'child' => 'Hijo/a',
            'friend' => 'Amigo/a',
            'partner' => 'Pareja',
            'other' => 'Otro',
        ];
    }

    /**
     * Accessor: Etiqueta de relación
     */
    public function getRelationshipLabelAttribute()
    {
        $types = self::getRelationshipTypes();
        return $types[$this->relationship] ?? $this->relationship;
    }

    /**
     * Método: Validar que el empleado tenga al menos un contacto primario
     */
    public static function ensurePrimaryContact($employeeId, $exceptContactId = null)
    {
        $query = self::where('employee_id', $employeeId)
            ->where('is_primary', true);

        if ($exceptContactId) {
            $query->where('id', '!=', $exceptContactId);
        }

        return $query->exists();
    }
}
