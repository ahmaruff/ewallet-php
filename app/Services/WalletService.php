<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function createWalletForUser(User $user, float $initialBalance = 0.00): ?Wallet
    {
        return DB::transaction(function () use ($user, $initialBalance) {
            $existingWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if ($existingWallet) {
                return null;
            }

            $wallet = new Wallet([
                'user_id' => $user->id,
                'balance' => $initialBalance,
            ]);

            $wallet->save();
            return $wallet;
        });
    }

    private function changeBalance(Wallet $wallet, float $amount): bool
    {
        $wallet->increment('balance', $amount);
        return $wallet->save();
    }

    public function depositFunds(Wallet $wallet, float $amount, ?string $description = null): ?Transaction
    {
        if ($amount <= 0) {
            throw new Exception("Amount value cannot be less than or equal to 0");
        }

        return DB::transaction(function () use ($wallet, $amount, $description) {
            $trxData = [
                'wallet_id' => $wallet->id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $amount,
                'description' => $description
            ];

            $transaction = Transaction::create($trxData);

            if (!$this->changeBalance($wallet, $amount)) {
                throw new Exception("Failed to update wallet balance for deposit.");
            }

            return $transaction;
        });
    }

    public function withdrawFunds(Wallet $wallet, float $amount, ?string $description = null): ?Transaction
    {
        if ($amount <= 0) {
            throw new Exception("Amount value cannot be less than or equal to 0");
        }

        if ($amount > $wallet->balance) {
            throw new Exception("Amount value cannot be greater than wallet balance");
        }

        return DB::transaction(function () use ($wallet, $amount, $description) {
            $trxData = [
                'wallet_id' => $wallet->id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => $amount,
                'description' => $description
            ];

            $transaction = Transaction::create($trxData);

            if (!$this->changeBalance($wallet, -$amount)) {
                throw new Exception("Failed to update wallet balance for withdrawal.");
            }

            return $transaction;
        });
    }
}