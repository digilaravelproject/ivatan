<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveContentPurchase;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminWalletController extends Controller
{
    /**
     * View global platform revenue metrics.
     */
    public function revenueStats(): JsonResponse
    {
        // Total revenue generated from platform fees on completed purchases
        $totalPlatformRevenue = ExclusiveContentPurchase::where('status', 'completed')
            ->sum('platform_fee_charged');

        $totalGatewayFees = ExclusiveContentPurchase::where('status', 'completed')
            ->sum('gateway_charge_amount');

        $totalCreatorEarnings = ExclusiveContentPurchase::where('status', 'completed')
            ->sum('creator_price');

        return response()->json([
            'total_platform_revenue' => $totalPlatformRevenue,
            'total_gateway_fees' => $totalGatewayFees,
            'total_creator_earnings' => $totalCreatorEarnings,
        ]);
    }

    /**
     * List all creator wallets.
     */
    public function listWallets(): JsonResponse
    {
        $wallets = Wallet::with('user:id,name,username')
            ->orderBy('balance', 'desc')
            ->paginate(20);

        return response()->json($wallets);
    }

    /**
     * List all wallet transactions globally or filtered.
     */
    public function allTransactions(Request $request): JsonResponse
    {
        $query = WalletTransaction::with(['wallet.user:id,name,username']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(30);

        return response()->json($transactions);
    }
}
