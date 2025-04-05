<?php

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;

test('create wallet for the user', function() {
    $user = User::factory()->create();
    $walletService = app(WalletService::class);

    $wallet = $walletService->createWalletForUser($user);

    expect($wallet)->toBeInstanceOf(Wallet::class);
    expect($wallet->user_id)->toBe($user->id);
    expect((string) $wallet->balance)->toBe('0.00'); // Assuming default balance.  compare as string
});

test('create wallet for the user - returns null if wallet already exists', function () {
    $user = User::factory()->create();
    $walletService = app(WalletService::class);

    // Create the first wallet
    $walletService->createWalletForUser($user);

    // Attempt to create a second wallet for the same user
    $secondWallet = $walletService->createWalletForUser($user);

    expect($secondWallet)->toBeNull();
});

test('user deposit money, then balance increased', function() {
    $user = User::factory()->create();
    $walletService = app(WalletService::class);

    $wallet = $walletService->createWalletForUser($user,0.00);

    $amount = 100000.00;

    $transaction = $walletService->depositFunds($wallet, $amount, 'user deposit 100.000');

    expect($transaction)->toBeInstanceOf(Transaction::class);
    expect($transaction->type)->toBe(Transaction::TYPE_DEPOSIT);
    expect((string) $transaction->amount)->toBe('100000.00');
    $wallet->refresh();

    expect($wallet->balance)->toBe('100000.00');
});

test('user deposit money less than or equal to 0, throw error amount', function () {
    $user = User::factory()->create();
    $walletService = app(WalletService::class);

    $wallet = $walletService->createWalletForUser($user, 0.00);
    $amount = 0;

    expect(fn () => $walletService->depositFunds($wallet, $amount, 'user deposit 0'))
        ->toThrow(Exception::class, 'Amount value cannot be less than or equal to 0');

    $amount = -50000;

    expect(fn () => $walletService->depositFunds($wallet, $amount, 'user deposit -50000'))
        ->toThrow(Exception::class, 'Amount value cannot be less than or equal to 0');
});