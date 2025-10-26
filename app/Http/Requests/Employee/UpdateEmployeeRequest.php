<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('El nombre no puede estar vacío.');
                    }
                    if (preg_match('/^(\d)\1+$/', trim($value))) {
                        $fail('El nombre no puede ser solo números repetidos.');
                    }
                    if (preg_match('/^([a-zA-Z])\1{2,}$/', trim($value))) {
                        $fail('El nombre no puede ser solo letras repetidas.');
                    }
                },
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('El apellido no puede estar vacío.');
                    }
                    if (preg_match('/^(\d)\1+$/', trim($value))) {
                        $fail('El apellido no puede ser solo números repetidos.');
                    }
                    if (preg_match('/^([a-zA-Z])\1{2,}$/', trim($value))) {
                        $fail('El apellido no puede ser solo letras repetidas.');
                    }
                },
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:employees,email,' . $employeeId,
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'min:7',
                'max:20',
                'regex:/^[\d\s\+\-\(\)]+$/',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('/^(\d)\1+$/', preg_replace('/[\s\+\-\(\)]/', '', $value))) {
                        $fail('El teléfono no puede ser solo números repetidos.');
                    }
                },
            ],
            'hire_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:1950-01-01',
            ],
            'salary' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
                function ($attribute, $value, $fail) {
                    if ($value < 500000) {
                        $fail('El salario parece demasiado bajo (mínimo recomendado: $500,000).');
                    }
                    if ($value > 50000000) {
                        $fail('El salario parece inusualmente alto. Verifica el valor.');
                    }
                },
            ],
            'status' => [
                'required',
                'in:active,inactive,on_leave,terminated',
            ],
            'birth_date' => [
                'nullable',
                'date',
                'before:today',
                'after:1920-01-01',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $birthDate = \Carbon\Carbon::parse($value);
                        $age = $birthDate->age;
                        
                        if ($age < 18) {
                            $fail('El empleado debe ser mayor de 18 años.');
                        }
                        if ($age > 100) {
                            $fail('La fecha de nacimiento parece incorrecta.');
                        }
                    }
                },
            ],
            'address' => [
                'nullable',
                'string',
                'min:10',
                'max:500',
            ],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
            'position_id' => [
                'required',
                'integer',
                'exists:positions,id',
                function ($attribute, $value, $fail) {
                    $position = \App\Models\Position::find($value);
                    if ($position && !$position->is_active) {
                        $fail('El puesto seleccionado no está activo.');
                    }
                },
            ],
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
                function ($attribute, $value, $fail) {
                    $department = \App\Models\Department::find($value);
                    if ($department && !$department->is_active) {
                        $fail('El departamento seleccionado no está activo.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'first_name.regex' => 'El nombre solo puede contener letras.',
            
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.min' => 'El apellido debe tener al menos 2 caracteres.',
            'last_name.regex' => 'El apellido solo puede contener letras.',
            
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Ingrese un email válido.',
            'email.unique' => 'Este email ya está registrado.',
            
            'phone.min' => 'El teléfono debe tener al menos 7 caracteres.',
            'phone.regex' => 'El formato del teléfono no es válido.',
            
            'hire_date.required' => 'La fecha de contratación es obligatoria.',
            'hire_date.before_or_equal' => 'La fecha de contratación no puede ser futura.',
            
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número válido.',
            'salary.min' => 'El salario no puede ser negativo.',
            
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser JPG, JPEG o PNG.',
            'photo.max' => 'La imagen no puede superar los 2MB.',
            
            'position_id.required' => 'Debe seleccionar un puesto.',
            'position_id.exists' => 'El puesto seleccionado no existe.',
            
            'department_id.required' => 'Debe seleccionar un departamento.',
            'department_id.exists' => 'El departamento seleccionado no existe.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->first_name ? ucwords(strtolower(trim($this->first_name))) : null,
            'last_name' => $this->last_name ? ucwords(strtolower(trim($this->last_name))) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'address' => $this->address ? trim($this->address) : null,
            'salary' => $this->salary !== null && $this->salary !== '' ? (float) $this->salary : null,
        ]);
    }
}