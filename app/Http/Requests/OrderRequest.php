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
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');

        $orderRules = [
            'user_id' => [$isCreating ? 'required' : 'sometimes', 'integer', 'exists:users,id'],
            'table_id' => [$isCreating ? 'required' : 'sometimes', 'integer', 'exists:tables,id'],
            'status' => [
                $isCreating ? 'required' : 'sometimes',
                'string',
                Rule::in(['pagado', 'pendiente', 'en preparacion', 'cancelado']),
            ],
            'payment_method' => ['nullable', 'required_if:status,pagado', 'string', Rule::in(['efectivo', 'tarjeta'])],
        ];

        $itemRules = [
            'items' => ['present', 'array'],

            'items.*.id' => ['nullable', 'integer', 'exists:order_items,id,order_id,'.$this->order?->id],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'items.*.status' => ['required', 'string', Rule::in(['enviado', 'en preparacion', 'cancelado'])],
        ];

        return array_merge($orderRules, $itemRules);
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El camarero es obligatorio.',
            'user_id.exists' => 'El camarero seleccionado no es válido.',
            'table_id.required' => 'La mesa es obligatoria.',
            'table_id.exists' => 'La mesa seleccionada no es válida.',
            'payment_method.required_if' => 'El método de pago es obligatorio cuando el pedido está pagado.',
            'status.in' => 'El estado seleccionado no es válido.',

            'items.*.menu_item_id.required' => 'El producto para el item #:position es obligatorio.',
            'items.*.menu_item_id.exists' => 'El producto para el item #:position no es válido.',
            'items.*.quantity.required' => 'La cantidad para el item #:position es obligatoria.',
            'items.*.quantity.min' => 'La cantidad para el item #:position debe ser al menos 1.',
        ];
    }
}
