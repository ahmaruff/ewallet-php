<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role can be assigned to user', function () {
    $user = User::factory()->create();
    $adminRole = Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);

    $user->roles()->attach($adminRole->id);
    $user = $user->load('roles');

    expect($user->hasRole(Role::ROLE_ADMIN))->toBeTrue();
});

test('return false is user does not have the role', function() {
    $user = User::factory()->create();
    $user = $user->load('roles');

    $adminRole = Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);

    $user->roles()->attach($adminRole->id);

    expect($user->hasRole(Role::ROLE_USER))->toBeFalse();
});

test('role can be unassigned from user', function() {
    $user = User::factory()->create();

    $adminRole = Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);

    $user->roles()->attach($adminRole->id);
    $user = $user->load('roles');
    expect($user->hasRole(Role::ROLE_ADMIN))->toBeTrue();

    $user->roles()->detach($adminRole->id);
    $user = $user->load('roles');
    expect($user->hasRole(Role::ROLE_ADMIN))->toBeFalse();
});