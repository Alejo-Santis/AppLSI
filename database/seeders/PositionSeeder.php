<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'title' => 'Desarrollador Junior',
                'description' => 'Desarrollo de software con supervisión. Responsable de tareas básicas de programación y mantenimiento.',
                'level' => 'junior',
                'min_salary' => 2500000,
                'max_salary' => 3500000,
                'is_active' => true,
            ],
            [
                'title' => 'Desarrollador Full Stack',
                'description' => 'Desarrollo de aplicaciones web completas, frontend y backend.',
                'level' => 'mid',
                'min_salary' => 4000000,
                'max_salary' => 6000000,
                'is_active' => true,
            ],
            [
                'title' => 'Desarrollador Senior',
                'description' => 'Desarrollo de arquitecturas complejas, mentoría de equipo y decisiones técnicas.',
                'level' => 'senior',
                'min_salary' => 7000000,
                'max_salary' => 10000000,
                'is_active' => true,
            ],
            [
                'title' => 'Tech Lead',
                'description' => 'Liderazgo técnico del equipo, arquitectura y coordinación de proyectos.',
                'level' => 'lead',
                'min_salary' => 10000000,
                'max_salary' => 14000000,
                'is_active' => true,
            ],
            [
                'title' => 'Analista de Recursos Humanos',
                'description' => 'Gestión de procesos de reclutamiento, nómina y bienestar laboral.',
                'level' => 'mid',
                'min_salary' => 3500000,
                'max_salary' => 5000000,
                'is_active' => true,
            ],
            [
                'title' => 'Gerente de Recursos Humanos',
                'description' => 'Dirección estratégica del departamento de recursos humanos.',
                'level' => 'manager',
                'min_salary' => 8000000,
                'max_salary' => 12000000,
                'is_active' => true,
            ],
            [
                'title' => 'Diseñador UX/UI',
                'description' => 'Diseño de interfaces y experiencia de usuario para productos digitales.',
                'level' => 'mid',
                'min_salary' => 4000000,
                'max_salary' => 6500000,
                'is_active' => true,
            ],
            [
                'title' => 'Product Manager',
                'description' => 'Gestión del ciclo de vida del producto, roadmap y coordinación con equipos.',
                'level' => 'manager',
                'min_salary' => 9000000,
                'max_salary' => 13000000,
                'is_active' => true,
            ],
            [
                'title' => 'Analista de Datos',
                'description' => 'Análisis de datos, reportes y visualizaciones para toma de decisiones.',
                'level' => 'mid',
                'min_salary' => 4500000,
                'max_salary' => 6500000,
                'is_active' => true,
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Automatización, CI/CD, infraestructura cloud y monitoreo.',
                'level' => 'senior',
                'min_salary' => 7500000,
                'max_salary' => 11000000,
                'is_active' => true,
            ],
            [
                'title' => 'Director de Tecnología (CTO)',
                'description' => 'Dirección estratégica de tecnología y equipos de desarrollo.',
                'level' => 'director',
                'min_salary' => 15000000,
                'max_salary' => 25000000,
                'is_active' => true,
            ],
            [
                'title' => 'Contador',
                'description' => 'Gestión contable, declaraciones tributarias y estados financieros.',
                'level' => 'mid',
                'min_salary' => 3800000,
                'max_salary' => 5500000,
                'is_active' => true,
            ],
            [
                'title' => 'Asistente Administrativo',
                'description' => 'Soporte administrativo general, gestión de documentos y coordinación.',
                'level' => 'junior',
                'min_salary' => 2000000,
                'max_salary' => 3000000,
                'is_active' => true,
            ],
            [
                'title' => 'Gerente de Ventas',
                'description' => 'Dirección del equipo comercial y estrategias de ventas.',
                'level' => 'manager',
                'min_salary' => 7000000,
                'max_salary' => 10000000,
                'is_active' => true,
            ],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
