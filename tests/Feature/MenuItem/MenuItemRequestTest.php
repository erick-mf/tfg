<?php

use App\Http\Requests\MenuItemRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Validator;

test('validation rules contain all required fields with correct rules', function () {
    $request = new MenuItemRequest;
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['name', 'price', 'is_available', 'image_path', 'menu_category_id'])
        ->and($rules['name'])->toContain('required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u')
        ->and($rules['price'])->toContain('required', 'numeric', 'min:0')
        ->and($rules['is_available'])->toContain('boolean')
        ->and($rules['image_path'])->toContain('nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048')
        ->and($rules['menu_category_id'])->toContain('required');
});

test('prepareForValidation formats name correctly', function () {
    $request = new MenuItemRequest;
    $request->merge(['name' => 'HAmBurgeR', 'price' => 10.5678]);
    $request->prepareForValidation();

    expect($request->name)->toBe('Hamburger');
    expect($request->price)->toBe(10.57);
});

test('validates successfully with valid data', function () {
    $category = MenuCategory::factory()->create();

    $data = [
        'name' => 'Platos Principales',
        'price' => 15.99,
        'is_available' => true,
        'menu_category_id' => $category->id,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->passes())->toBeTrue();
});

test('name field validation', function ($value, $rule) {
    $category = MenuCategory::factory()->create();

    $data = [
        'name' => $value,
        'price' => 10.99,
        'is_available' => true,
        'image_path' => 'image.jpg',
        'menu_category_id' => $category->id,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('name');
    expect($validator->failed()['name'])->toHaveKey($rule);
})->with([
    'empty' => ['', 'Required'],
    'too short' => ['Ab', 'Min'],
    'too long' => [str_repeat('a', 101), 'Max'],
    'invalid characters' => ['Plat0$', 'Regex'],
]);

test('price field validation', function ($value, $rule) {
    $category = MenuCategory::factory()->create();

    $data = [
        'name' => 'Plato Válido',
        'price' => $value,
        'is_available' => true,
        'image_path' => 'image.jpg',
        'menu_category_id' => $category->id,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('price');
    expect($validator->failed()['price'])->toHaveKey($rule);
})->with([
    'empty' => [null, 'Required'],
    'non-numeric' => ['invalid', 'Numeric'],
    'negative' => [-5, 'Min'],
]);

test('is_available field validation', function ($value, $rule) {
    $category = MenuCategory::factory()->create();

    $data = [
        'name' => 'Plato Válido',
        'price' => 10.99,
        'is_available' => $value,
        'image_path' => 'image.jpg',
        'menu_category_id' => $category->id,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('is_available');
    expect($validator->failed()['is_available'])->toHaveKey($rule);
})->with([
    'non-boolean' => ['invalid', 'Boolean'],
]);

test('menu_category_id is required', function () {
    $data = [
        'name' => 'Plato Válido',
        'price' => 10.99,
        'image_path' => 'image.jpg',
        'is_available' => true,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('menu_category_id');
    expect($validator->failed()['menu_category_id'])->toHaveKey('Required');
});

test('name must be unique in creation', function () {
    $category = MenuCategory::factory()->create();
    $item = MenuItem::factory()->create(['menu_category_id' => $category->id]);

    $data = [
        'name' => $item->name,
        'price' => 15.99,
        'is_available' => true,
        'image_path' => 'image.jpg',
        'menu_category_id' => $category->id,
    ];

    $validator = Validator::make($data, (new MenuItemRequest)->rules());
    expect($validator->fails())->toBeTrue();
    expect($validator->failed())->toHaveKey('name');
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('name unique rule ignores current id during update', function () {
    $category = MenuCategory::factory()->create();
    $item = MenuItem::factory()->create(['menu_category_id' => $category->id]);

    $rules = [
        'name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s]+$/u', \Illuminate\Validation\Rule::unique('menu_items')->ignore($item->id)],
        'price' => ['required', 'numeric', 'min:0'],
        'is_available' => ['boolean'],
        'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        'menu_category_id' => ['required'],
    ];

    $validator = Validator::make(
        [
            'name' => $item->name,
            'price' => 15.99,
            'is_available' => true,
            'menu_category_id' => $category->id,
        ],
        $rules
    );

    expect($validator->passes())->toBeTrue();
});
