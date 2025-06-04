<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TableRequest extends FormRequest
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
    public function prepareForValidation()
    {
        $this->merge([
            'name' => $this->name ? ucwords(strtolower($this->name)) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\s]+$/', Rule::unique('tables')->ignore($this->id)],
            'status' => ['required', 'in:disponible,ocupada,reservada,en limpieza'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre debe tener como máximo 50 caracteres.',
            'name.regex' => 'El nombre solo puede contener letras, números y espacios.',
            'name.unique' => 'El nombre debe ser único.',

            'status.required' => 'El status es requerido.',
            'status.in' => 'El status debe ser disponible, ocupada, reservada o en limpieza.',
        ];
    }
}
