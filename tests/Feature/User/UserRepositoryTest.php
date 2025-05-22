<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

beforeEach(function () {
    $this->repository = app(UserRepositoryInterface::class);
});

afterEach(function () {
    \Mockery::close();
});

test('paginate returns user collection', function () {
    User::factory()->count(15)->create();

    $result = $this->repository->paginate(10);

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(10);
});

test('findById returns correct user', function () {
    $user = User::factory()->create();

    $found = $this->repository->findById($user->id);

    expect($found)
        ->toBeInstanceOf(User::class)
        ->id->toBe($user->id);
});

test('findById returns null for non-existent user', function () {
    $found = $this->repository->findById(999);

    expect($found)->toBeNull();
});

test('create user successfully', function () {
    $userData = [
        'name' => 'Test User',
        'surnames' => 'Test Surnames',
        'phone' => '612345678',
        'role' => 'camarero',
        'password' => bcrypt('123456'),
    ];

    $user = $this->repository->create($userData);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Test User');

    expect(User::count())->toBe(1);
});

test('update user successfully', function () {
    $user = User::factory()->create(['name' => 'Old Name']);

    $updatedUser = $this->repository->update(
        ['name' => 'New Name'],
        $user
    );

    expect($updatedUser)
        ->toBeInstanceOf(User::class)
        ->name->toBe('New Name');
});

test('update throws exception on failure', function () {
    $this->mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('update')
            ->once()
            ->andThrow(new Exception('Error updating user'));
    });

    $user = User::factory()->create();

    app(UserRepositoryInterface::class)
        ->update(['name' => 'New Name'], $user);
})->throws(Exception::class, 'Error updating user');

test('delete user successfully', function () {
    $user = User::factory()->create();

    $result = $this->repository->delete($user);

    expect($result)->toBeTrue();
    expect(User::count())->toBe(0);
});

test('delete throws exception on failure', function () {
    $this->mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('delete')
            ->once()
            ->andThrow(new Exception('Error deleting user'));
    });

    $user = User::factory()->create();

    app(UserRepositoryInterface::class)
        ->delete($user);
})->throws(Exception::class, 'Error deleting user');
