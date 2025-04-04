<?php

use App\Models\User;

test('create wallet for the user', function() {
    $user = User::factory()->create();
    expect($user->wallet)->isNotEmpty();
});
