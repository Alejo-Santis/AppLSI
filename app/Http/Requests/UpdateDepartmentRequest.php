<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|unique:departments,name',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($this->id),
            ],
            'description' => 'nullable|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'budget' => 'nullable|numeric|min:0|max:999999999999999999',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del departamento es obligatorio',
            'code.required' => 'El código es obligatorio',
            'code.unique' => 'Este código ya está en uso',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción debe tener un máximo de 255 caracteres.',
            'description.regex' => 'La descripción debe contener solo letras y sin espacios.',
            'budget.numeric' => 'El presupuesto debe ser un número válido',
            'budget.min' => 'El presupuesto no puede ser negativo',
            'budget.max' => 'El presupuesto no puede ser mayor a 999999999999999999',
            'manager_id.exists' => 'El empleado seleccionado no existe',
        ];
    }
}
