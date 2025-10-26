<?php

namespace App\Http\Requests\Position;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Position;

class StorePositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\/\-]+$/', // Solo letras, espacios, / y -
                function ($attribute, $value, $fail) {
                    // Validar que no sea solo espacios
                    if (trim($value) === '') {
                        $fail('El título no puede estar vacío o contener solo espacios.');
                    }
                    
                    // Validar que no sea solo números repetidos
                    if (preg_match('/^(\d)\1+$/', trim($value))) {
                        $fail('El título no puede ser solo números repetidos (ej: 111111).');
                    }
                    
                    // Validar que no sea solo letras repetidas
                    if (preg_match('/^([a-zA-Z])\1{4,}$/', trim($value))) {
                        $fail('El título no puede ser solo letras repetidas (ej: aaaaa).');
                    }
                    
                    // Validar que tenga al menos 2 palabras significativas para puestos gerenciales
                    $words = array_filter(explode(' ', trim($value)));
                    if (count($words) < 2 && strlen(trim($value)) < 10) {
                        $fail('El título debe ser más descriptivo. Ejemplo: "Desarrollador Frontend" o "Analista de Datos".');
                    }
                    
                    // Validar que cada palabra tenga al menos 2 caracteres
                    foreach ($words as $word) {
                        if (strlen($word) < 2 && $word !== 'de' && $word !== 'en' && $word !== 'y') {
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
                        // Validar que no sea solo espacios
                        if (trim($value) === '') {
                            $fail('La descripción no puede estar vacía o contener solo espacios.');
                        }
                        
                        // Validar que tenga contenido significativo (al menos 5 palabras)
                        $words = str_word_count(trim($value));
                        if ($words < 5) {
                            $fail('La descripción debe contener al menos 5 palabras para ser significativa.');
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
                        $fail('El salario mínimo parece demasiado bajo. Verifica el valor ingresado.');
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
                        // Validar que la diferencia sea razonable (máximo 10x)
                        $ratio = $value / $minSalary;
                        if ($ratio > 10) {
                            $fail('El salario máximo no puede ser más de 10 veces el salario mínimo. Verifica los valores.');
                        }
                        
                        // Validar que la diferencia mínima sea al menos 10%
                        $difference = (($value - $minSalary) / $minSalary) * 100;
                        if ($difference < 10) {
                            $fail('El rango salarial debe tener al menos un 10% de diferencia entre mínimo y máximo.');
                        }
                    }
                    
                    // Validar que el salario máximo no sea ridículamente bajo
                    if ($value !== null && $value < 600000) {
                        $fail('El salario máximo parece demasiado bajo. Verifica el valor ingresado.');
                    }
                },
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título del puesto es obligatorio.',
            'title.min' => 'El título debe tener al menos 5 caracteres.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'title.regex' => 'El título solo puede contener letras, espacios, guiones y diagonales.',
            
            'description.min' => 'La descripción debe tener al menos 20 caracteres para ser útil.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            
            'level.required' => 'El nivel del puesto es obligatorio.',
            'level.in' => 'El nivel seleccionado no es válido. Debe ser: junior, mid, senior, lead, manager o director.',
            
            'min_salary.numeric' => 'El salario mínimo debe ser un número válido.',
            'min_salary.min' => 'El salario mínimo no puede ser negativo.',
            'min_salary.max' => 'El salario mínimo excede el límite permitido.',
            
            'max_salary.numeric' => 'El salario máximo debe ser un número válido.',
            'max_salary.min' => 'El salario máximo no puede ser negativo.',
            'max_salary.max' => 'El salario máximo excede el límite permitido.',
            'max_salary.gte' => 'El salario máximo debe ser mayor o igual al salario mínimo.',
            
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'título del puesto',
            'description' => 'descripción',
            'level' => 'nivel',
            'min_salary' => 'salario mínimo',
            'max_salary' => 'salario máximo',
            'is_active' => 'estado',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar y formatear datos antes de validar
        $this->merge([
            'title' => $this->title ? ucwords(strtolower(trim($this->title))) : null,
            'description' => $this->description ? trim($this->description) : null,
            'level' => $this->level ? strtolower(trim($this->level)) : null,
            'min_salary' => $this->min_salary !== null && $this->min_salary !== '' ? (float) $this->min_salary : null,
            'max_salary' => $this->max_salary !== null && $this->max_salary !== '' ? (float) $this->max_salary : null,
            'is_active' => $this->is_active ?? true,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Normalizar el título después de la validación
        $this->merge([
            'title' => trim($this->title),
        ]);
    }
}