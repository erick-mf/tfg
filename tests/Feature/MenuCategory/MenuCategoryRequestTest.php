<?php

use App\Http\Requests\MenuCategoryRequest;
use Illuminate\Support\Facades\Validator;

test('validation rules for MenuCategoryRequest are correct', function () {
    $request = new MenuCategoryRequest;

    $rules = $request->rules();
    expect($rules)->toHaveKeys(['name']);

    expect($rules['name'])->toContain('required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u');
});

test('name is required when creating menu category', function () {
    $request = new MenuCategoryRequest;
    $request->setMethod('POST');

    $rules = $request->rules();
    expect($rules['name'])->toContain('required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u');
});

test('prepareValidation formats name correctly', function () {
    $request = new MenuCategoryRequest;

    $request->merge([
        'name' => 'pLaTos',
    ]);

    $request->prepareForValidation();
    expect($request->name)->toBe('Platos');
});

test('name is required', function () {
    $data = [
        'name' => '',
    ];

    $validator = Validator::make($data, (new MenuCategoryRequest)->rules(), (new MenuCategoryRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid characters', function () {
    $data = [
        'name' => 'Post$es',
    ];

    $validator = Validator::make($data, (new MenuCategoryRequest)->rules(), (new MenuCategoryRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid length less than 3', function () {
    $data = [
        'name' => 'C',
    ];

    $validator = Validator::make($data, (new MenuCategoryRequest)->rules(), (new MenuCategoryRequest)->messages());

    expect($validator->fails())->toBeTrue();
});

test('name validation fails with invalid length greater than 100', function () {
    $data = [
        'name' => 'Cooooooooooooooooooooooooooiiiiiiiiiiiiiiiiiiiiiiiiinnnnnnnnnnnnnnnnnnnnnnnnnnaaaaaaaaaaaaaaaaaaaaaaaaa',
    ];

    $validator = Validator::make($data, (new MenuCategoryRequest)->rules(), (new MenuCategoryRequest)->messages());

    expect($validator->fails())->toBeTrue();
});
