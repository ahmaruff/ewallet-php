<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role can be assigned to user', function () {
    $user = User::factory()->create();
    $adminRole = Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);

    $user->roles()->attach($adminRole->id);

    expect($user->hasRole(Role::ROLE_ADMIN))->toBeTrue();
});
