<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'surnames' => $this->surnames ? ucwords(strtolower($this->surnames)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'surnames' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\p{L}\s]+$/u'],
            'phone' => ['required',  'size:9', 'regex:/^[0-9]+$/'],
            'phone_emergency' => ['nullable',  'size:9', 'regex:/^[0-9]+$/'],
            'role' => ['required', 'in:encargado,camarero,cocinero'],
        ];
        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'size:6', Rule::unique('users')];
        } elseif ($this->filled('password')) {
            $rules['password'] = ['string', 'size:6', Rule::unique('users')];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.min' => 'El campo nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El campo nombre debe tener como máximo 100 caracteres.',
            'name.regex' => 'El campo nombre solo puede contener letras y espacios.',

            'surnames.required' => 'El campo apellidos es obligatorio.',
            'surnames.string' => 'El campo apellidos debe ser una cadena de texto.',
            'surnames.min' => 'El campo apellidos debe tener al menos 3 caracteres.',
            'surnames.max' => 'El campo apellidos debe tener como máximo 150 caracteres.',
            'surnames.regex' => 'El campo apellidos solo puede contener letras y espacios.',

            'phone.required' => 'El campo teléfono es obligatorio.',
            'phone.size' => 'El campo teléfono debe tener 9 dígitos.',
            'phone.regex' => 'El campo teléfono solo puede contener números.',

            'phone_emergency.size' => 'El campo teléfono de emergencia debe tener 9 dígitos.',
            'phone_emergency.regex' => 'El campo teléfono de emergencia solo puede contener números.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'El campo contraseña debe ser una cadena de texto.',
            'password.size' => 'El campo contraseña debe tener 6 dígitos.',

            'role.required' => 'El campo rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
