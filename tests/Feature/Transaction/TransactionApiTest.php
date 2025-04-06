<?php

use App\Models\User;
use App\Services\PaymentGatewayService;
use App\Services\WalletService;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('api deposit successfully', function() {
    // Mock the PaymentGatewayService
    $paymentGatewayServiceMock = Mockery::mock(PaymentGatewayService::class);
    $paymentGatewayServiceMock->shouldReceive('deposit')
        ->once()
        ->andReturn([
            'order_id' => '12345',
            'amount' => 100000,
            'status' => 1, // Success
        ]);

    // Bind the mock PaymentGatewayService to the container
    $this->app->instance(PaymentGatewayService::class, $paymentGatewayServiceMock);


    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->postJson('/api/transactions/deposit',[
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

    $user->load('wallet');
    $wallet = $user->wallet->refresh();
    expect($wallet->balance)->toBe('100000.00');
});

test('api withdraw successfully', function() {
    $user = User::factory()->create();
    $this->actingAs($user);

    $walletService = app(WalletService::class);

    $wallet = $walletService->createWalletForUser($user,100000.00);

    $amount = 10000.00;

    $response = $this->postJson('/api/transactions/withdraw',[
        'amount' => $amount
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

    $user->load('wallet');
    $w = $user->wallet->refresh();
    expect($w->balance)->toBe('90000.00');
});