<?php

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
