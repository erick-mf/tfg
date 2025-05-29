<?php

use App\Models\Location;
use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(ProductRepositoryInterface::class);
});

test('paginate return correct number of items per page', function () {
    Location::factory()->count(10)->create();
    Product::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(10);
    expect($result->total())->toBe(15);
    expect($result->items())->each->toBeInstanceOf(Product::class);
});

test('findById returns correct product', function () {
    Location::factory()->create();
    $product = Product::factory()->create();

    $found = $this->repository->findById($product->id);
    expect($found)->toBeInstanceOf(Product::class);
    expect($found->id)->toBe($product->id);
});

test('findById returns null when product not found', function () {
    $found = $this->repository->findById(999);
    expect($found)->toBeNull();
});

test('create product successfully', function () {
    $location = Location::factory()->create();
    $productData = ['name' => 'Vino', 'unit' => 'litro', 'stock' => '10', 'location_id' => $location->id];
    $product = $this->repository->create($productData);

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->name->toBe('Vino');
});

test('create product fails when name exists', function () {
    $location = Location::factory()->create();
    $productData = ['name' => 'Vino', 'unit' => 'litro', 'stock' => '10', 'location_id' => $location->id];
    $this->repository->create($productData);

    expect(fn () => $this->repository->create($productData))
        ->toThrow(RuntimeException::class);
});

test('update product successfully', function () {
    $location = Location::factory()->create();
    $productData = ['name' => 'Old product', 'unit' => 'litro', 'stock' => '10', 'location_id' => $location->id];
    $product = $this->repository->create($productData);
    $updated = $this->repository->update(['name' => 'New product'], $product);

    expect($updated)->toBeTrue();
    expect($product->fresh()->name)->toBe('New product');
});

test('update product fails when name exists', function () {
    $location = Location::factory()->create();
    $productData1 = ['name' => 'Product one', 'unit' => 'litro', 'stock' => '10', 'location_id' => $location->id];
    $productData2 = ['name' => 'Product two', 'unit' => 'litro', 'stock' => '10', 'location_id' => $location->id];
    $product1 = $this->repository->create($productData1);
    $product2 = $this->repository->create($productData2);

    expect(fn () => $this->repository->update(
        ['name' => 'Product one'],
        $product2
    ))->toThrow(RuntimeException::class);
});

test('update menu category with same name succeeds', function () {
    $location = Location::factory()->create();
    $product = $this->repository->create(['name' => 'Product one', 'is_available' => true, 'location_id' => $location->id]);
    $result = $this->repository->update(['name' => 'Product one', 'is_available' => false, 'location_id' => $location->id], $product);

    expect($result)->toBeTrue();
});

test('delete menu category successfully', function () {
    $location = Location::factory()->create();
    $product = Product::factory()->create(['name' => 'Product one', 'is_available' => true, 'location_id' => $location->id]);
    $result = $this->repository->delete($product);

    expect($result)->toBeTrue();
});

test('delete non-existent product throws exception', function () {
    $location = Location::factory()->create();
    $product = Product::factory()->create(['name' => 'Product', 'is_available' => true, 'location_id' => $location->id]);
    $product->forceDelete();

    expect(fn () => $this->repository->delete($product))
        ->toThrow(RuntimeException::class);
});

test('soft delete non-existent product throws exception', function () {
    $location = Location::factory()->create();
    $product = Product::factory()->create(['name' => 'Product', 'is_available' => true, 'location_id' => $location->id]);

    $result = $this->repository->delete($product);
    expect($result)->toBeTrue();
});
