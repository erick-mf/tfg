<?php

use App\Http\Requests\LocationRequest;
use Illuminate\Support\Facades\Validator;

test('validation rules for LocationRequest are correct', function () {
    $request = new LocationRequest;

    $rules = $request->rules();
    expect($rules)->toHaveKeys(['name']);

    expect($rules['name'])->toContain('required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u');
});

test('name is required when creating location', function () {
    $request = new LocationRequest;
    $request->setMethod('POST');

    $rules = $request->rules();
    expect($rules['name'])->toContain('required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u');
});

test('prepareValidation formats name correctly', function () {
    $request = new LocationRequest;

    $request->merge([
        'name' => 'CocInA',
    ]);

    $request->prepareForValidation();

    expect($request->name)->toBe('Cocina');
});

test('validation messages are correct', function () {
    $request = new LocationRequest;
    $messages = $request->messages();

    expect($messages)->toBeArray();
    expect($messages)->toHaveKeys([
        'name.required', 'name.string', 'name.min', 'name.max', 'name.regex',
    ]);
});

test('valid data passes validation', function () {
    $data = [
        'name' => 'Cocina',
    ];

    $validator = Validator::make($data, (new LocationRequest)->rules(), (new LocationRequest)->messages());

    expect($validator->fails())->toBeFalse();
});

test('name is required', function () {
    $data = [
        'name' => '',
    ];

    $validator = Validator::make($data, (new LocationRequest)->rules(), (new LocationRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid characters', function () {
    $data = [
        'name' => 'Cocin@',
    ];

    $validator = Validator::make($data, (new LocationRequest)->rules(), (new LocationRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid length less than 3', function () {
    $data = [
        'name' => 'C',
    ];

    $validator = Validator::make($data, (new LocationRequest)->rules(), (new LocationRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid length greater than 100', function () {
    $data = [
        'name' => 'Cooooooooooooooooooooooooooiiiiiiiiiiiiiiiiiiiiiiiiinnnnnnnnnnnnnnnnnnnnnnnnnnaaaaaaaaaaaaaaaaaaaaaaaaa',
    ];

    $validator = Validator::make($data, (new LocationRequest)->rules(), (new LocationRequest)->messages());

    expect($validator->fails())->toBeTrue();
});
