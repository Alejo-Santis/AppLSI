<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Project::with('projectManager')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'code',
            'description',
            'start_date',
            'end_date',
            'status',
            'budget',
            'project_manager_id',
            'project_manager_name',
            'days_remaining',
            'progress_percentage',
            'created_at',
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->name,
            $project->code,
            $project->description,
            $project->start_date?->format('Y-m-d'),
            $project->end_date?->format('Y-m-d'),
            $project->status,
            $project->budget,
            $project->project_manager_id,
            $project->projectManager ? $project->projectManager->first_name . ' ' . $project->projectManager->last_name : '',
            $project->days_remaining,
            $project->progress_percentage . '%',
            $project->created_at->format('Y-m-d'),
        ];
    }
}
