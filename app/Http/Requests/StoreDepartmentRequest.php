<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
            'code' => 'required|string|max:255|unique:departments,code',
            'description' => 'nullable|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'budget' => 'nullable|numeric|min:0|max:999999999999999999',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El nombre debe contener solo letras y sin espacios.',
            'name.unique' => 'El nombre ya existe.',
            'code.required' => 'El código es obligatorio.',
            'code.regex' => 'El código debe contener solo letras y sin espacios.',
            'code.unique' => 'El código ya existe.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción debe tener un máximo de 255 caracteres.',
            'description.regex' => 'La descripción debe contener solo letras y sin espacios.',
            'budget.numeric' => 'El presupuesto debe ser un número.',
            'budget.min' => 'El presupuesto debe ser mayor o igual a 0.',
            'budget.max' => 'El presupuesto debe ser menor o igual a 999999999999999999.',
            'manager_id.exists' => 'El ID del gerente no es válido.',
            'is_active.required' => 'El estado es obligatorio.',
            'is_active.boolean' => 'El estado debe ser un booleano.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'code' => 'código',
            'description' => 'descripción',
            'budget' => 'presupuesto',
            'manager_id' => 'gerente',
            'is_active' => 'estado',
        ];
    }
}
