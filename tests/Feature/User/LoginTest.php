<?php

use App\Models\User;

test('loads login page successfully', function () {
    $this->get(route('login'))->assertOk();
});

test('successful login redirects to dashboard', function () {
    $admin = User::factory()->create([
        'password' => '12345a',
        'role' => 'admin',
    ]);

    $this->post(route('login'), ['password' => '12345a'])
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('toast.type', 'success');

    $this->assertAuthenticatedAs($admin);
});

test('logout redirects to login page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('password field is required', function () {
    $this->post(route('login'), ['password' => ''])
        ->assertSessionHasErrors(['password']);
});

test('password must be exactly 6 characters', function () {
    $this->post(route('login'), ['password' => '1234'])
        ->assertSessionHasErrors(['password']);

    $this->post(route('login'), ['password' => '1234567a'])
        ->assertSessionHasErrors(['password']);
});

test('password must be alphanumeric', function () {
    $this->post(route('login'), ['password' => '12345!'])
        ->assertSessionHasErrors(['password']);

    $this->post(route('login'), ['password' => 'abcd@#'])
        ->assertSessionHasErrors(['password']);
});

test('incorrect password shows error', function () {
    $this->post(route('login'), ['password' => '23456a'])
        ->assertSessionHasErrors(['password']);

    $this->assertGuest();
});

test('login with valid credentials authenticates user', function () {
    $user = User::factory()->create([
        'password' => 'abc123',
        'role' => 'admin',
    ]);

    $this->post(route('login'), ['password' => 'abc123'])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});
