<?php

test('loads login page successfully', function () {
    $this->get(route('login'))->assertOk();
});

test('successful login redirects to dashboard', function () {
    $this->post(route('login.submit'), ['password' => '12345a'])
        ->assertRedirect(route('admin.dashboard'));
});

test('logout redirects to home', function () {
    $this->post(route('logout'))->assertRedirect(route('login'));
});

test('password field is required', function () {
    $this->post(route('login'), ['password' => ''])
        ->assertSessionHasErrors(['password' => 'La contraseña es obligatoria.']);
});

test('password must be exactly 6 characters', function () {
    $this->post(route('login'), ['password' => '1234'])
        ->assertSessionHasErrors(['password' => 'La longitud de la contraseña no es correcta']);
});

test('password must be alphanumeric', function () {
    $this->post(route('login'), ['password' => '12345!'])
        ->assertSessionHasErrors(['password' => 'La contraseña debe ser alfanumérica']);
});

test('incorrect password shows error', function () {
    $this->post(route('login'), ['password' => '23456a'])
        ->assertSessionHasErrors(['password' => 'La contraseña es incorrecta']);
});
