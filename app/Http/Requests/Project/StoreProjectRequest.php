<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\/]+$/',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('El nombre del proyecto no puede estar vacío.');
                    }
                    if (preg_match('/^(\d)\1+$/', trim($value))) {
                        $fail('El nombre no puede ser solo números repetidos.');
                    }
                    if (preg_match('/^([a-zA-Z])\1{4,}$/', trim($value))) {
                        $fail('El nombre no puede ser solo letras repetidas.');
                    }
                    $words = array_filter(explode(' ', trim($value)));
                    if (count($words) < 2 && strlen(trim($value)) < 10) {
                        $fail('El nombre debe ser más descriptivo (mínimo 2 palabras o 10 caracteres).');
                    }
                },
            ],
            'code' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[A-Z0-9\-_]+$/',
                'unique:projects,code',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^\d+$/', $value)) {
                        $fail('El código no puede contener solo números.');
                    }
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('El código debe contener al menos una letra mayúscula.');
                    }
                },
            ],
            'description' => [
                'nullable',
                'string',
                'min:20',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        if (trim($value) === '') {
                            $fail('La descripción no puede estar vacía o contener solo espacios.');
                        }
                        $words = str_word_count(trim($value));
                        if ($words < 5) {
                            $fail('La descripción debe contener al menos 5 palabras.');
                        }
                    }
                },
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:2020-01-01',
                function ($attribute, $value, $fail) {
                    $startDate = \Carbon\Carbon::parse($value);
                    $today = \Carbon\Carbon::today();
                    
                    if ($startDate->gt($today->copy()->addYears(2))) {
                        $fail('La fecha de inicio no puede ser mayor a 2 años en el futuro.');
                    }
                },
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $startDate = $this->input('start_date');
                        if ($startDate) {
                            $start = \Carbon\Carbon::parse($startDate);
                            $end = \Carbon\Carbon::parse($value);
                            
                            $diffInDays = $start->diffInDays($end);
                            
                            if ($diffInDays < 1) {
                                $fail('El proyecto debe durar al menos 1 día.');
                            }
                            
                            if ($diffInDays > 3650) { // 10 años
                                $fail('La duración del proyecto no puede exceder los 10 años.');
                            }
                        }
                    }
                },
            ],
            'status' => [
                'required',
                'string',
                'in:planning,active,on_hold,completed,cancelled',
            ],
            'budget' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value > 0 && $value < 100000) {
                        $fail('El presupuesto parece demasiado bajo para un proyecto. Verifica el valor.');
                    }
                },
            ],
            'project_manager_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $employee = \App\Models\Employee::find($value);
                        if ($employee && $employee->status !== 'active') {
                            $fail('El empleado seleccionado debe estar activo.');
                        }
                        
                        // Verificar que no tenga demasiados proyectos activos como manager
                        $activeProjects = \App\Models\Project::where('project_manager_id', $value)
                            ->whereIn('status', ['planning', 'active'])
                            ->count();
                        
                        if ($activeProjects >= 5) {
                            $fail('Este empleado ya gestiona 5 proyectos activos. Considera asignar otro manager.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del proyecto es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras, números, espacios, guiones y diagonales.',
            
            'code.required' => 'El código del proyecto es obligatorio.',
            'code.min' => 'El código debe tener al menos 4 caracteres.',
            'code.max' => 'El código no puede exceder los 50 caracteres.',
            'code.regex' => 'El código solo puede contener letras mayúsculas, números, guiones y guiones bajos.',
            'code.unique' => 'Este código ya está siendo utilizado por otro proyecto.',
            
            'description.min' => 'La descripción debe tener al menos 20 caracteres.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser posterior a 2020.',
            
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            
            'status.required' => 'El estado del proyecto es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            
            'budget.numeric' => 'El presupuesto debe ser un número válido.',
            'budget.min' => 'El presupuesto no puede ser negativo.',
            'budget.max' => 'El presupuesto excede el límite permitido.',
            
            'project_manager_id.integer' => 'El ID del manager debe ser un número entero.',
            'project_manager_id.exists' => 'El empleado seleccionado no existe.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? ucwords(strtolower(trim($this->name))) : null,
            'code' => $this->code ? strtoupper(trim($this->code)) : null,
            'description' => $this->description ? trim($this->description) : null,
            'status' => $this->status ? strtolower(trim($this->status)) : null,
            'budget' => $this->budget !== null && $this->budget !== '' ? (float) $this->budget : null,
        ]);
    }
}