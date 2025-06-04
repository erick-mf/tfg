<?php

use App\Models\Table;
use App\Repositories\Table\TableRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(TableRepositoryInterface::class);
});

test('paginate return correct number of items per page', function () {
    Table::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($result->items())->toHaveCount(10);
    expect($result->total())->toBe(15);
    expect($result->items())->each->toBeInstanceOf(Table::class);
});

test('findById returns correct table', function () {
    Table::factory()->create();
    $table = Table::factory()->create();

    $found = $this->repository->findById($table->id);
    expect($found)->toBeInstanceOf(Table::class);
    expect($found->id)->toBe($table->id);
});

test('findById returns null when table not found', function () {
    $found = $this->repository->findById(999);
    expect($found)->toBeNull();
});

test('create table successfully', function () {
    $tableData = ['name' => 'Mesa 1', 'status' => 'Ocupada'];
    $table = $this->repository->create($tableData);

    expect($table)
        ->toBeInstanceOf(Table::class)
        ->name->toBe('Mesa 1');
});

test('create table fails when name exists', function () {
    $tableData = ['name' => 'Mesa 1', 'status' => 'Ocupada'];
    $this->repository->create($tableData);

    expect(fn () => $this->repository->create($tableData))
        ->toThrow(RuntimeException::class);
});

test('update table successfully', function () {
    $tableData = ['name' => 'Mesa 1', 'status' => 'Ocupada'];

    $table = $this->repository->create($tableData);
    $updated = $this->repository->update(['name' => 'New Mesa'], $table);

    expect($updated)->toBeTrue();
    expect($table->fresh()->name)->toBe('New Mesa');
});

test('update table fails when name exists', function () {
    $tableData1 = ['name' => 'Mesa 1', 'status' => 'Ocupada'];
    $tableData2 = ['name' => 'Mesa 2', 'status' => 'Ocupada'];
    $table1 = $this->repository->create($tableData1);
    $table2 = $this->repository->create($tableData2);

    expect(fn () => $this->repository->update(
        ['name' => 'Mesa 1'],
        $table2
    ))->toThrow(RuntimeException::class);
});

test('update menu item with same name succeeds', function () {
    $table = $this->repository->create(['name' => 'Mesa 1', 'status' => 'Ocupada']);

    $result = $this->repository->update(['name' => 'Mesa 2', 'status' => 'Disponible'], $table);

    expect($result)->toBeTrue();
});

test('delete menu category successfully', function () {
    $table = $this->repository->create(['name' => 'Mesa 1', 'status' => 'Ocupada']);
    $result = $this->repository->delete($table);

    expect($result)->toBeTrue();
});

test('delete non-existent table throws exception', function () {
    $table = $this->repository->create(['name' => 'Mesa 1', 'status' => 'Ocupada']);
    $table->forceDelete();

    expect(fn () => $this->repository->delete($table))
        ->toThrow(RuntimeException::class);
});

test('soft delete non-existent table throws exception', function () {
    $table = $this->repository->create(['name' => 'Mesa 1', 'status' => 'Ocupada']);

    $result = $this->repository->delete($table);
    expect($result)->toBeTrue();
});
