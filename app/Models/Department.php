<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'budget',
        'is_active',
        'manager_id',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Manager Relation
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Employee Relation
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope: Search by name and code
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
     * Accesor Employees Numbers
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Accessor Active Employees
     */
    public function getActiveEmployeesCountAttribute()
    {
        return $this->employees()->where('status', 'active')->count();
    }

    /**
     * Method Verify can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->employees()->count() === 0;
    }

    /**
     * Method generate unique automatic code
     */
    public static function generateCode(): string
    {
        $lastDepartment = self::orderBy('id', 'desc')->first();
        $number = $lastDepartment ? (int) substr($lastDepartment->code, 4) + 1 : 1;
        return 'DEPT' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
