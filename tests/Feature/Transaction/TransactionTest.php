<?php

use App\Models\User;

test('users can visit the transaction pages', function() {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/transactions');
    $response->assertStatus(200);
});