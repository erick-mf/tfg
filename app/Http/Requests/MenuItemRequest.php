<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemRequest extends FormRequest
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
            'name' => $this->name ? ucfirst(strtolower(trim($this->name))) : null,
            'price' => $this->price ? round($this->price, 2) : null,
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
            'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u', Rule::unique('menu_items')->ignore($this->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'location' => ['required'],
            'menu_category_id' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre debe tener como máximo 100 caracteres.',
            'name.regex' => 'El nombre debe contener solo letras y espacios.',
            'name.unique' => 'El nombre ya existe.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',

            'is_available.boolean' => 'El estado de disponibilidad debe ser un valor valido.',

            'image_path.image' => 'La imagen debe ser una imagen.',
            'image_path.mimes' => 'La imagen debe tener uno de los siguientes formatos: jpeg, png, jpg, webp.',
            'image_path.max' => 'La imagen debe tener un tamaño máximo de 2MB.',

            'location.required' => 'La ubicación es obligatoria.',

            'menu_category_id.required' => 'La categoría es obligatoria.',
        ];
    }
}
