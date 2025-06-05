<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
        $rulse = [
            'user_id' => 'required|exists:users,id',
            'table_id' => 'required|exists:tables,id',
            'status' => ['required', 'in:pendiente,en preparacion,pagado', Rule::unique('orders')->ignore($this->status, 'status')],
        ];
        if (isset($this->status)) {
            if ($this->status == 'pagado') {
                $rulse['payment_method'] = 'required';
            }
        }

        return $rulse;
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El camarero es obligatorio.',
            'user_id.exists' => 'El camarero no existe.',

            'table_id.required' => 'La mesa es obligatoria.',
            'table_id.exists' => 'La mesa no existe.',

            'payment_method.required' => 'El metodo de pago es obligatorio.',

            'status.required' => 'El status es obligatorio.',
            'status.in' => 'El status debe ser uno de los siguientes: pendiente, en preparacion, pagado, cancelado.',
        ];
    }
}
