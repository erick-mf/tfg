<?php

use App\Models\MenuCategory;
use App\Repositories\MenuCategory\MenuCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(MenuCategoryRepositoryInterface::class);
});

test('paginate returns correct number of items per page', function () {
    MenuCategory::factory()->count(8)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(8);
    expect($result->total())->toBe(8);
    expect($result->items())->each->toBeInstanceOf(MenuCategory::class);
});

test('findById returns correct menu category', function () {
    $menuCategory = MenuCategory::factory()->create();

    $found = $this->repository->findById($menuCategory->id);
    expect($found)->toBeInstanceOf(MenuCategory::class);
    expect($found->id)->toBe($menuCategory->id);
});

test('findById returns null when menu category not found', function () {
    $found = $this->repository->findById(999);
    expect($found)->toBeNull();
});

test('create menu category successfully', function () {
    $menuCategoryData = ['name' => 'Platos'];
    $menuCategory = $this->repository->create($menuCategoryData);

    expect($menuCategory)
        ->toBeInstanceOf(MenuCategory::class)
        ->name->toBe('Platos');
});

test('create menu category fails when name exists', function () {
    $menuCategoryData = ['name' => 'Postres'];
    $this->repository->create($menuCategoryData);

    // Solo verificamos el tipo de excepción, no el mensaje
    expect(fn () => $this->repository->create($menuCategoryData))
        ->toThrow(RuntimeException::class);
});

test('update menu category successfully', function () {
    $menuCategory = MenuCategory::factory()->create(['name' => 'Old Menu Category']);
    $updated = $this->repository->update(['name' => 'New Menu Category'], $menuCategory);

    expect($updated)->toBeTrue();
    expect($menuCategory->fresh()->name)->toBe('New Menu Category');
});

test('update menu category fails when name exists in other category', function () {
    $menuCategory1 = $this->repository->create(['name' => 'Menu Category One']);
    $menuCategory2 = $this->repository->create(['name' => 'Menu Category Two']);

    expect(fn () => $this->repository->update(
        ['name' => 'Menu Category One'],
        $menuCategory2
    ))->toThrow(RuntimeException::class);
});

test('update menu category with same name succeeds', function () {
    $menuCategory = $this->repository->create(['name' => 'Menu Category One']);
    $result = $this->repository->update(['name' => 'Menu Category One'], $menuCategory);

    expect($result)->toBeTrue();
});

test('delete menu category successfully', function () {
    $menuCategory = MenuCategory::factory()->create();
    $result = $this->repository->delete($menuCategory);

    expect($result)->toBeTrue();
});

test('delete non-existent menu category throws exception', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuCategory->delete();

    expect(fn () => $this->repository->delete($menuCategory))
        ->toThrow(RuntimeException::class);
});
