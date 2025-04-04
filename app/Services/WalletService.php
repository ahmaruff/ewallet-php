<?php
namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function createWalletForUser(User $user, float $initialBalance = 0.00) : ?Wallet
    {
        DB::beginTransaction();
        try {
            $existingWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if ($existingWallet) {
                return null;
            }

            $wallet = new Wallet([
                'user_id' => $user->id,
                'balance' => $initialBalance,
            ]);

            $wallet->save();
            DB::commit();
            return $wallet;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}