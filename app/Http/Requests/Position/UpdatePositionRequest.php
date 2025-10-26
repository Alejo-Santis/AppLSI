<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Position;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $positionId = $this->route('id');

        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\/\-]+$/',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('El título no puede estar vacío o contener solo espacios.');
                    }
                    
                    if (preg_match('/^(\d)\1+$/', trim($value))) {
                        $fail('El título no puede ser solo números repetidos.');
                    }
                    
                    if (preg_match('/^([a-zA-Z])\1{4,}$/', trim($value))) {
                        $fail('El título no puede ser solo letras repetidas.');
                    }
                    
                    $words = array_filter(explode(' ', trim($value)));
                    if (count($words) < 2 && strlen(trim($value)) < 10) {
                        $fail('El título debe ser más descriptivo.');
                    }
                    
                    foreach ($words as $word) {
                        if (strlen($word) < 2 && !in_array($word, ['de', 'en', 'y', 'a'])) {
                            $fail('Cada palabra en el título debe tener al menos 2 caracteres.');
                        }
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
            'level' => [
                'required',
                'string',
                'in:junior,mid,senior,lead,manager,director',
            ],
            'min_salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value < 500000) {
                        $fail('El salario mínimo parece demasiado bajo. Verifica el valor.');
                    }
                },
            ],
            'max_salary' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
                'gte:min_salary',
                function ($attribute, $value, $fail) {
                    $minSalary = $this->input('min_salary');
                    
                    if ($value !== null && $minSalary !== null) {
                        $ratio = $value / $minSalary;
                        if ($ratio > 10) {
                            $fail('El salario máximo no puede ser más de 10 veces el mínimo.');
                        }
                        
                        $difference = (($value - $minSalary) / $minSalary) * 100;
                        if ($difference < 10) {
                            $fail('El rango salarial debe tener al menos 10% de diferencia.');
                        }
                    }
                    
                    if ($value !== null && $value < 600000) {
                        $fail('El salario máximo parece demasiado bajo. Verifica el valor.');
                    }
                },
            ],
            'is_active' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) use ($positionId) {
                    if ($value === false || $value === 0 || $value === '0') {
                        $position = Position::find($positionId);
                        if ($position) {
                            $activeEmployees = $position->employees()->where('status', 'active')->count();
                            if ($activeEmployees > 0) {
                                $fail("No se puede desactivar el puesto porque tiene {$activeEmployees} empleado(s) activo(s) asignado(s).");
                            }
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del puesto es obligatorio.',
            'title.min' => 'El título debe tener al menos 5 caracteres.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'title.regex' => 'El título solo puede contener letras, espacios, guiones y diagonales.',
            
            'description.min' => 'La descripción debe tener al menos 20 caracteres.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            
            'level.required' => 'El nivel del puesto es obligatorio.',
            'level.in' => 'El nivel seleccionado no es válido.',
            
            'min_salary.numeric' => 'El salario mínimo debe ser un número válido.',
            'min_salary.min' => 'El salario mínimo no puede ser negativo.',
            'min_salary.max' => 'El salario mínimo excede el límite permitido.',
            
            'max_salary.numeric' => 'El salario máximo debe ser un número válido.',
            'max_salary.min' => 'El salario máximo no puede ser negativo.',
            'max_salary.max' => 'El salario máximo excede el límite permitido.',
            'max_salary.gte' => 'El salario máximo debe ser mayor o igual al mínimo.',
            
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->title ? ucwords(strtolower(trim($this->title))) : null,
            'description' => $this->description ? trim($this->description) : null,
            'level' => $this->level ? strtolower(trim($this->level)) : null,
            'min_salary' => $this->min_salary !== null && $this->min_salary !== '' ? (float) $this->min_salary : null,
            'max_salary' => $this->max_salary !== null && $this->max_salary !== '' ? (float) $this->max_salary : null,
        ]);
    }
}