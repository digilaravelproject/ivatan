<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorWalletController extends Controller
{
    /**
     * Get Creator Wallet Balance.
     */
    public function balance(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $wallet = $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );

        return response()->json([
            'balance' => $wallet->balance,
            'status' => $wallet->status,
        ]);
    }

    /**
     * Get Creator Wallet Transactions.
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $wallet = $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->with(['buyer:id,name,username', 'content:id,caption,type'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }
}
