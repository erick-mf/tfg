<?php

use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Validator;

test('validation fails when name is missing', function () {
    $request = new ProductRequest;
    $request->setMethod('POST');

    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('validation fails when name is too short', function () {
    $request = new ProductRequest;
    $request->setMethod('POST');

    $data = ['name' => 'ab'];
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('validation fails when name contains numbers', function () {
    $request = new ProductRequest;
    $request->setMethod('POST');

    $data = ['name' => 'tomate123'];
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('validation passes with valid name', function () {
    $request = new ProductRequest;
    $request->setMethod('POST');

    $data = [
        'name' => 'Tomates',
        'is_available' => true,
        'location_id' => 1,
    ];

    $rules = $request->rules();
    unset($rules['location_id']); // Remover por simplicidad en el test

    $validator = Validator::make($data, $rules);

    expect($validator->fails())->toBeFalse();
});

test('prepareValidation formats data correctly', function () {
    $request = new ProductRequest;
    $request->merge([
        'name' => 'tomates cherry',
    ]);

    $request->prepareForValidation();

    expect($request->name)->toBe('Tomates Cherry');
});

test('prepareValidation handles null values', function () {
    $request = new ProductRequest;
    $request->merge([
        'name' => null,
    ]);

    $request->prepareForValidation();

    expect($request->name)->toBeNull();
});
