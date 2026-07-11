<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Get or create a wallet for the given user.
     */
    public function getWallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'status' => 'active']
        );
    }

    /**
     * Credit an amount to the user's wallet.
     */
    public function credit(int $userId, float $amount, string $referenceType, int $referenceId, ?string $description = null, ?int $buyerId = null, ?int $contentId = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Credit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($userId, $amount, $referenceType, $referenceId, $description, $buyerId, $contentId) {
            // Lock the wallet row for update
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = $this->getWallet($userId);
            }

            if ($wallet->status !== 'active') {
                throw new Exception("Wallet is not active.");
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $wallet->balance + $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'buyer_id' => $buyerId,
                'content_id' => $contentId,
                'status' => 'completed',
            ]);
        });
    }

    /**
     * Debit an amount from the user's wallet.
     */
    public function debit(int $userId, float $amount, string $referenceType, int $referenceId, ?string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Debit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($userId, $amount, $referenceType, $referenceId, $description) {
            // Lock the wallet row for update
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = $this->getWallet($userId);
            }

            if ($wallet->status !== 'active') {
                throw new Exception("Wallet is not active.");
            }

            if ($wallet->balance < $amount) {
                throw new Exception("Insufficient balance.");
            }

            $balanceBefore = $wallet->balance;
            $balanceAfter = $wallet->balance - $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'description' => $description,
                'status' => 'completed',
            ]);
        });
    }
}
