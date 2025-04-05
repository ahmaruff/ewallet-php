<?php

use App\Models\User;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('api deposit successfully', function() {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->postJson('/api/transactions/deposit',[
        'user_id' => $user->id,
        'amount' => 100000
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'code',
            'message',
            'data' => [
                'transaction',
                'user_id',
                'balance'
            ],
        ]);

    $wallet = $user->wallet;
    expect($wallet->balance)->toBe('100000.00');
});