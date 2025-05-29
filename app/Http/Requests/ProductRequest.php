<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => $this->name ? ucwords(strtolower($this->name)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u', Rule::unique('products')->ignore($this->id)],
            'is_available' => ['required', 'boolean'],
            'location_id' => ['required', 'exists:locations,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener menos de 100 caracteres',
            'name.regex' => 'El nombre solo puede contener letras y espacios',
            'name.unique' => 'El nombre ya está en uso',

            'is_available.required' => 'El estado es obligatorio',
            'is_available.boolean' => 'El estado debe ser verdadero o falso',

            'location_id.required' => 'La ubicación es obligatoria',
            'location_id.exists' => 'La ubicación no existe',
        ];
    }
}
