<?php

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Repositories\MenuItem\MenuItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(MenuItemRepositoryInterface::class);
});

test('paginate return correct number of items per page', function () {
    MenuCategory::factory()->count(10)->create();
    MenuItem::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(10);
    expect($result->total())->toBe(15);
    expect($result->items())->each->toBeInstanceOf(MenuItem::class);
});

test('findById returns correct menuItem', function () {
    MenuCategory::factory()->create();
    $menuItem = MenuItem::factory()->create();

    $found = $this->repository->findById($menuItem->id);
    expect($found)->toBeInstanceOf(MenuItem::class);
    expect($found->id)->toBe($menuItem->id);
});

test('findById returns null when menuItem not found', function () {
    $found = $this->repository->findById(999);
    expect($found)->toBeNull();
});

test('create menuItem successfully', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItemData = ['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id];
    $menuItem = $this->repository->create($menuItemData);

    expect($menuItem)
        ->toBeInstanceOf(MenuItem::class)
        ->name->toBe('Hamburguesa');
});

test('create menuItem fails when name exists', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItemData = ['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id];
    $this->repository->create($menuItemData);

    expect(fn () => $this->repository->create($menuItemData))
        ->toThrow(RuntimeException::class);
});

test('update menuItem successfully', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItemData = ['name' => 'Old Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id];
    $menuItem = $this->repository->create($menuItemData);
    $updated = $this->repository->update(['name' => 'New Hamburguesa'], $menuItem);

    expect($updated)->toBeTrue();
    expect($menuItem->fresh()->name)->toBe('New Hamburguesa');
});

test('update menuItem fails when name exists', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItemData1 = ['name' => 'Hamburguesa1', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id];
    $menuItemData2 = ['name' => 'Hamburguesa2', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id];
    $menuItem1 = $this->repository->create($menuItemData1);
    $menuItem2 = $this->repository->create($menuItemData2);

    expect(fn () => $this->repository->update(
        ['name' => 'Hamburguesa1'],
        $menuItem2
    ))->toThrow(RuntimeException::class);
});

test('update menu item with same name succeeds', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItem = $this->repository->create(['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id]);
    $result = $this->repository->update(['name' => 'Hamburguesa', 'price' => 20, 'is_available' => false, 'menu_category_id' => $menuCategory->id], $menuItem);

    expect($result)->toBeTrue();
});

test('delete menu category successfully', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItem = $this->repository->create(['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id]);
    $result = $this->repository->delete($menuItem);

    expect($result)->toBeTrue();
});

test('delete non-existent menuItem throws exception', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItem = $this->repository->create(['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id]);
    $menuItem->forceDelete();

    expect(fn () => $this->repository->delete($menuItem))
        ->toThrow(RuntimeException::class);
});

test('soft delete non-existent menuItem throws exception', function () {
    $menuCategory = MenuCategory::factory()->create();
    $menuItem = $this->repository->create(['name' => 'Hamburguesa', 'price' => 10, 'is_available' => true, 'menu_category_id' => $menuCategory->id]);

    $result = $this->repository->delete($menuItem);
    expect($result)->toBeTrue();
});
