<?php

namespace Tests\Feature\Location;

use App\Models\Location;
use App\Repositories\Location\LocationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->repository = app(LocationRepositoryInterface::class);
});

test('paginate returns correct number of items per page', function () {
    Location::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);

    expect($result->items())->toHaveCount(10);

    expect($result->total())->toBe(15);

    expect($result->items())->each->toBeInstanceOf(Location::class);

});

test('findById returns correct location', function () {
    $location = Location::factory()->create();

    $found = $this->repository->findById($location->id);

    expect($found)
        ->toBeInstanceOf(Location::class)
        ->id->toBe($location->id);
});

test('findById returns null for non-existent localtion', function () {
    $found = $this->repository->findById(999);

    expect($found)->toBeNull();
});

test('create location successfully', function () {
    $locationData = [
        'name' => 'New Location',
    ];

    $location = $this->repository->create($locationData);

    expect($location)
        ->toBeInstanceOf(Location::class)
        ->name->toBe('New Location');

    expect(Location::count())->toBe(1);
});

test('create location fails validation', function () {
    $locationData = [
        'name' => 'location one',
    ];

    $this->repository->create($locationData);

    $duplicateLocationData = [
        'name' => 'location one', ];

    expect(fn () => $this->repository->create($duplicateLocationData))->toThrow('Location already exists');
});

test('update location successfully', function () {
    $location = Location::factory()->create(['name' => 'Old Location']);

    $updatedLocation = $this->repository->update(
        ['name' => 'New Location'],
        $location
    );

    expect($updatedLocation)
        ->toBeInstanceOf(Location::class)
        ->name->toBe('New Location');
});

test('update location fails validation', function () {
    $location1 = $this->repository->create(['name' => 'Location One']);
    $location2 = $this->repository->create(['name' => 'Location Two']);

    $result = $this->repository->update(['name' => 'Location One'], $location2);

    expect($result)->toBeInstanceOf(Location::class);
    expect($result->name)->toBe('Location One');

    $this->assertDatabaseCount('locations', 2);
    $this->assertDatabaseHas('locations', [
        'id' => $location1->id,
        'name' => 'Location One',
    ]);
    $this->assertDatabaseHas('locations', [
        'id' => $location2->id,
        'name' => 'Location One',
    ]);
});

test('delete location successfully', function () {
    $location = Location::factory()->create();

    $result = $this->repository->delete($location);

    expect($result)->toBeTrue();
    expect(Location::count())->toBe(0);

});
