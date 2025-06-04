<?php

use App\Http\Requests\TableRequest;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;

test('validation rules contain all required fields with correct rules', function () {
    $request = new TableRequest;
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['name', 'status'])
        ->and($rules['name'])->toContain('required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\s]+$/')
        ->and($rules['status'])->toContain('required', 'in:disponible,ocupada,reservada,en limpieza');
});

test('prepareForValidation formats name correctly', function () {
    $request = new TableRequest;
    $request->merge(['name' => 'meSA 1']);
    $request->prepareForValidation();

    expect($request->name)->toBe('Mesa 1');
});

test('validates successfully with valid data', function () {
    $data = [
        'name' => 'Mesa 1',
        'status' => 'ocupada',
    ];

    $validator = Validator::make($data, (new TableRequest)->rules());
    expect($validator->passes())->toBeTrue();
});

test('name field validation', function ($value, $rule) {
    $data = [
        'name' => $value,
        'status' => 'ocupada',
    ];

    $validator = Validator::make($data, (new TableRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('name');
    expect($validator->failed()['name'])->toHaveKey($rule);
})->with([
    'empty' => ['', 'Required'],
    'too short' => ['Me', 'Min'],
    'too long' => [str_repeat('a', 100), 'Max'],
    'invalid characters' => ['Mesa 1$', 'Regex'],
]);

test('status field validation', function ($value, $rule) {
    $data = [
        'name' => 'Plato Válido',
        'status' => $value,
    ];

    $validator = Validator::make($data, (new TableRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('status');
    expect($validator->failed()['status'])->toHaveKey($rule);
})->with([
    'empty' => [null, 'Required'],
    'invalid value' => ['invalid', 'In'],
]);

test('name must be unique in creation', function () {
    $item = Table::factory()->create(['name' => 'Mesa 1']);

    $data = [
        'name' => $item->name,
        'status' => 'disponible',
    ];

    $validator = Validator::make($data, (new TableRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('name');
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('name unique rule ignores current id during update', function () {
    $item = Table::factory()->create();

    $rules = [
        'name' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\s]+$/', \Illuminate\Validation\Rule::unique('tables')->ignore($item->id)],
        'status' => ['required', 'in:disponible,ocupada,reservada,en limpieza'],
    ];

    $validator = Validator::make(
        [
            'name' => $item->name,
            'status' => 'reservada',
        ],
        $rules
    );

    expect($validator->passes())->toBeTrue();
});
