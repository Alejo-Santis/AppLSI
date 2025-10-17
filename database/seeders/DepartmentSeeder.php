<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Tecnología',
                'code' => 'DEPT0001',
                'description' => 'Departamento de desarrollo de software, infraestructura y soporte técnico.',
                'budget' => 50000000,
                'is_active' => true,
                'manager_id' => null,
            ],
            [
                'name' => 'Recursos Humanos',
                'code' => 'DEPT0002',
                'description' => 'Gestión del talento humano, reclutamiento, nómina y bienestar laboral.',
                'budget' => 15000000,
                'is_active' => true,
                'manager_id' => null,
            ],
            [
                'name' => 'Finanzas y Contabilidad',
                'code' => 'DEPT0003',
                'description' => 'Gestión financiera, contabilidad, presupuestos y auditoría.',
                'budget' => 20000000,
                'is_active' => true,
                'manager_id' => null,
            ],
            [
                'name' => 'Ventas y Marketing',
                'code' => 'DEPT0004',
                'description' => 'Estrategias comerciales, marketing digital y atención al cliente.',
                'budget' => 30000000,
                'is_active' => true,
                'manager_id' => null,
            ],
            [
                'name' => 'Operaciones',
                'code' => 'DEPT0005',
                'description' => 'Gestión de procesos operativos, logística y administración general.',
                'budget' => 25000000,
                'is_active' => true,
                'manager_id' => null,
            ],
            [
                'name' => 'Diseño y Experiencia',
                'code' => 'DEPT0006',
                'description' => 'Diseño de productos, experiencia de usuario e identidad de marca.',
                'budget' => 18000000,
                'is_active' => true,
                'manager_id' => null,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
