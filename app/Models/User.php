<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'last_login',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación con Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Documentos subidos por este usuario
     */
    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Cambios salariales aprobados por este usuario
     */
    public function approvedSalaryChanges()
    {
        return $this->hasMany(SalaryHistory::class, 'approved_by');
    }

    /**
     * Logs de auditoría de este usuario
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Verificar si el usuario tiene el rol básico especificado
     */
    public function hasBasicRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Verificar si el usuario es admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('Admin');
    }

    /**
     * Verificar si el usuario es manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager' || $this->hasRole('Manager');
    }

    /**
     * Verificar si el usuario es HR
     */
    public function isHR(): bool
    {
        return $this->role === 'hr' || $this->hasRole('HR');
    }

    /**
     * Verificar si el usuario tiene rol de empleado
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Scope para filtrar usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por rol
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Obtener el nombre completo si tiene empleado asociado
     */
    public function getFullNameAttribute(): string
    {
        if ($this->employee) {
            return $this->employee->first_name.' '.$this->employee->last_name;
        }

        return $this->name;
    }

    /**
     * Obtener todos los permisos (incluye roles y permisos directos)
     */
    public function getAllPermissionsNames(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }
}
