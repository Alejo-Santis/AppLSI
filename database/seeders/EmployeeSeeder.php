<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $positions = Position::all();
        $departments = Department::all();

        // Crear 30 empleados de ejemplo
        for ($i = 1; $i <= 30; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();

            Employee::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower($firstName . '.' . $lastName . '@empresa.com'),
                'phone' => $faker->phoneNumber(),
                'hire_date' => $faker->dateTimeBetween('-5 years', 'now'),
                'salary' => $faker->numberBetween(2000000, 15000000),
                'status' => $faker->randomElement(['active', 'active', 'active', 'inactive', 'on_leave']), // más activos
                'birth_date' => $faker->dateTimeBetween('-50 years', '-22 years'),
                'address' => $faker->address(),
                'photo_url' => null,
                'position_id' => $positions->random()->id,
                'department_id' => $departments->random()->id,
            ]);
        }

        // Actualizar managers de departamentos con empleados existentes
        $techDept = Department::where('code', 'DEPT0001')->first();
        $hrDept = Department::where('code', 'DEPT0002')->first();
        $financeDept = Department::where('code', 'DEPT0003')->first();
        $salesDept = Department::where('code', 'DEPT0004')->first();

        // Asignar managers
        $techManager = Employee::where('department_id', $techDept->id)->first();
        if ($techManager) {
            $techDept->update(['manager_id' => $techManager->id]);
        }

        $hrManager = Employee::where('department_id', $hrDept->id)->first();
        if ($hrManager) {
            $hrDept->update(['manager_id' => $hrManager->id]);
        }

        $financeManager = Employee::where('department_id', $financeDept->id)->first();
        if ($financeManager) {
            $financeDept->update(['manager_id' => $financeManager->id]);
        }

        $salesManager = Employee::where('department_id', $salesDept->id)->first();
        if ($salesManager) {
            $salesDept->update(['manager_id' => $salesManager->id]);
        }
    }
}
