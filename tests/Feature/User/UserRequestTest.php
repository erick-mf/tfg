<?php

use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Validator;

test('validation rules for UserRequest are correct', function () {
    $request = new UserRequest;

    $rules = $request->rules();

    expect($rules)->toHaveKeys([
        'name', 'surnames', 'phone', 'phone_emergency', 'role',
    ]);

    expect($rules['name'])->toContain('required', 'string', 'min:3', 'max:100');
    expect($rules['surnames'])->toContain('required', 'string', 'min:3', 'max:150');
    expect($rules['phone'])->toContain('required', 'size:9');
    expect($rules['role'])->toContain('required', 'in:encargado,camarero,cocinero');
});

test('password is required when creating user', function () {
    $request = new UserRequest;
    $request->setMethod('POST');

    $rules = $request->rules();
    expect($rules['password'])->toContain('required', 'string', 'size:6');
});

test('password is optional when updating user', function () {
    $request = new UserRequest;
    $request->setMethod('PUT');
    $request->merge(['password' => '123456']);

    $rules = $request->rules();
    expect($rules['password'])->toContain('string', 'size:6');
});

test('phone_emergency is nullable', function () {
    $request = new UserRequest;

    $rules = $request->rules();
    expect($rules['phone_emergency'])->toContain('nullable');
});

test('prepareForValidation formats name and surnames correctly', function () {
    $request = new UserRequest;

    $request->merge([
        'name' => 'jOhN dOe',
        'surnames' => 'sMith jOnes',
    ]);

    $request->prepareForValidation();

    expect($request->name)->toBe('John Doe');
    expect($request->surnames)->toBe('Smith Jones');
});

test('validation messages are correct', function () {
    $request = new UserRequest;
    $messages = $request->messages();

    expect($messages)->toBeArray();
    expect($messages)->toHaveKeys([
        'name.required', 'name.string', 'name.min', 'name.max', 'name.regex',
        'surnames.required', 'surnames.string', 'surnames.min', 'surnames.max', 'surnames.regex',
        'phone.required', 'phone.size', 'phone.regex',
        'phone_emergency.size', 'phone_emergency.regex',
        'password.required', 'password.string', 'password.size',
        'role.required', 'role.in',
    ]);
});

test('name validation fails with invalid characters', function () {
    $data = [
        'name' => 'John123',
        'surnames' => 'Doe',
        'phone' => '123456789',
        'role' => 'encargado',
        'password' => '123456',
    ];

    $validator = Validator::make($data, (new UserRequest)->rules(), (new UserRequest)->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('El campo nombre solo puede contener letras y espacios.');
});

test('phone validation fails with invalid length', function () {
    $data = [
        'name' => 'John',
        'surnames' => 'Doe',
        'phone' => '12345',
        'role' => 'encargado',
        'password' => '123456',
    ];

    $validator = Validator::make($data, (new UserRequest)->rules(), (new UserRequest)->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('phone'))->toBe('El campo teléfono debe tener 9 dígitos.');
});

test('valid data passes validation', function () {
    $data = [
        'name' => 'María',
        'surnames' => 'González López',
        'phone' => '612345678',
        'phone_emergency' => '987654321',
        'role' => 'camarero',
        'password' => '12345a',
    ];

    $validator = Validator::make($data, (new UserRequest)->rules(), (new UserRequest)->messages());

    expect($validator->fails())->toBeFalse();
});
