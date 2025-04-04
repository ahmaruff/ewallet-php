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

    $response->assertRedirect(route('dashboard', absolute: false));

    // Assert that the success message is flashed to the session
    $response->assertSessionHas('success', 'User roles updated successfully.');

    // Optionally, you can also assert that there are no validation errors
    $response->assertSessionHasNoErrors();

    $user->load('roles');

    expect($user->hasRole(Role::ROLE_USER))->toBeFalse();
    expect($user->hasRole(Role::ROLE_ADMIN))->toBeTrue();
});

test('users get redirected back with errors on invalid input', function () {
    // Ensure 'user' and 'admin' roles exist
    Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);
    Role::firstOrCreate(['name' => Role::ROLE_USER]);

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/roles/update-user-roles', [
        'user_id' => $user->id,
        'add_role_name' => ['nonexistent-role'], // Invalid role name
    ]);

    $response->assertStatus(302);

    $response->assertRedirect('/'); // Adjust '/' to the expected previous URL if different

    // Assert that validation errors are flashed to the session
    $response->assertSessionHasErrors(['add_role_name.0']);

    $response->assertSessionHas('_old_input');

    // Further assertions to check that roles were NOT updated...
    $user->load('roles');
    expect($user->roles)->toBeEmpty();
});