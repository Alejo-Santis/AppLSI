<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario Admin principal
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@empresa.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'employee_id' => null,
        ]);

        // Usuario HR
        $hrEmployee = Employee::whereHas('department', function($query) {
            $query->where('code', 'DEPT0002');
        })->first();

        if ($hrEmployee) {
            User::create([
                'name' => $hrEmployee->first_name . ' ' . $hrEmployee->last_name,
                'email' => 'hr@empresa.com',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'is_active' => true,
                'employee_id' => $hrEmployee->id,
            ]);
        }

        // Usuario Manager
        $managerEmployee = Employee::whereHas('department', function($query) {
            $query->where('code', 'DEPT0001');
        })->first();

        if ($managerEmployee) {
            User::create([
                'name' => $managerEmployee->first_name . ' ' . $managerEmployee->last_name,
                'email' => 'manager@empresa.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'is_active' => true,
                'employee_id' => $managerEmployee->id,
            ]);
        }

        // Usuario Employee normal
        $employee = Employee::whereHas('department', function($query) {
            $query->where('code', 'DEPT0001');
        })->skip(1)->first();

        if ($employee) {
            User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => 'empleado@empresa.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'employee_id' => $employee->id,
            ]);
        }
    }
}
