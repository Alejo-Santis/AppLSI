<?php

namespace App\Exports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PositionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Position::select('title', 'description', 'level', 'min_salary', 'max_salary', 'is_active')->get();
    }

    public function headings(): array
    {
        return [
            'Title',
            'Description',
            'Level',
            'Min Salary',
            'Max Salary',
            'Is Active',
        ];
    }
}
