<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function debit(User $user, float $amount, string $description): bool
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            /** @var Wallet $wallet */
            $wallet = Wallet::lockForUpdate()->firstOrCreate(['user_id' => $user->id]);

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance to complete this upgrade.');
            }

            $wallet->decrement('balance', $amount);

            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
                'balance_after' => $wallet->fresh()->balance,
            ]);

            return true;
        });
    }

    public function credit(User $user, float $amount, string $description): bool
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = Wallet::lockForUpdate()->firstOrCreate(['user_id' => $user->id]);

            $wallet->increment('balance', $amount);

            $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
                'balance_after' => $wallet->fresh()->balance,
            ]);

            return true;
        });
    }
}
