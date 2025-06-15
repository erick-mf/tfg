<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WaiterOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return true;
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->route('order')->user_id === Auth::id();
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');

        $orderRules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'status' => ['required', 'string', Rule::in(['pendiente', 'pagado'])],
            'payment_method' => ['nullable', 'required_if:status,pagado', 'string', Rule::in(['efectivo', 'tarjeta'])],
        ];

        $itemRules = [
            'items' => ['present', 'array'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'items.*.status' => ['required', 'string', Rule::in(['enviado', 'cancelado', 'en preparacion', 'listo', 'pendiente'])],
        ];

        if (! $isCreating) {
            $orderId = $this->route('order')->id;
            $itemRules['items.*.id'] = ['nullable', 'integer', 'exists:order_items,id,order_id,'.$orderId];
        }

        return array_merge($orderRules, $itemRules);
    }
}
