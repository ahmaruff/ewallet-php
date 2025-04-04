<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users can register and are assigned the default role', function () {
    $defaultRoleName = Role::ROLE_USER;

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->first();
    $this->assertNotNull($user);

    $user = $user->load('roles');

    // Assert that the user has the default role
    $this->assertTrue($user->hasRole($defaultRoleName));
});

test('users can update role', function () {
    // Ensure 'user' and 'admin' roles exist
    Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);
    Role::firstOrCreate(['name' => Role::ROLE_USER]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->first();
    $this->assertNotNull($user);

    // Authenticate the user for the subsequent request
    $this->actingAs($user);

    // Assert the initial role (if registration assigns it)
    expect($user->hasRole('user'))->toBeTrue();

    $response = $this->post('/roles/update-user-roles', [
        'user_id' => $user->id,
        'add_role_name' => ['admin'],
        'remove_role_name' => ['user'],
    ]);

    $response->assertStatus(302); // redirect
    $user->load('roles');

    expect($user->hasRole(Role::ROLE_USER))->toBeFalse();
    expect($user->hasRole(Role::ROLE_ADMIN))->toBeTrue();
});
