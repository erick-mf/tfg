<?php

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

test('validation rules contain all required fields with correct rules', function () {
    $request = new OrderRequest;
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['user_id', 'table_id', 'status'])
        ->and($rules['user_id'])->toContain('required', 'exists:users,id')
        ->and($rules['table_id'])->toContain('required', 'exists:tables,id')
        ->and($rules['status'])->toContain('required', 'in:pendiente,en preparacion,pagado');
});

test('validates successfully with valid data', function () {
    $table = Table::factory()->create();
    $user = User::factory()->create();

    $data = [
        'user_id' => $user->id,
        'table_id' => $table->id,
        'payment_method' => 'efectivo',
        'status' => 'pagado',
    ];

    $validator = Validator::make($data, (new OrderRequest)->rules());
    expect($validator->passes())->toBeTrue();
});

test('status field validation', function ($value, $rule) {
    $data = [
        'status' => $value,
    ];

    $validator = Validator::make($data, (new OrderRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('status');
    expect($validator->failed()['status'])->toHaveKey($rule);
})->with([
    'empty' => ['', 'Required'],
    'invalid value' => ['invalid', 'In'],
]);

test('status unique rule ignores current id during update', function () {
    $user = User::factory()->create();
    $table = Table::factory()->create();
    $item = Order::factory()->create(['total' => 9.99, 'status' => 'pagado', 'user_id' => $user->id, 'table_id' => $table->id]);

    $rules = [
        'status' => ['required', 'in:pendiente,en preparacion,pagado', \Illuminate\Validation\Rule::unique('orders')->ignore($item->status, 'status')],
    ];

    $validator = Validator::make(
        [
            'status' => $item->status,
        ],
        $rules
    );

    expect($validator->passes())->toBeTrue();
});
