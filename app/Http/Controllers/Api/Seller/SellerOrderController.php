<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ecommerce\UserOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SellerOrderController extends Controller
{
    /**
     * List orders for the seller
     */
    public function index(Request $request): JsonResponse
    {
        $orders = UserOrder::with(['items', 'buyer'])
            ->where('seller_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Show order detail
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $order = UserOrder::with(['items', 'buyer', 'shipping', 'payment', 'address'])
            ->where('seller_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (Processing, Shipped, etc.)
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,completed,cancelled',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $order = UserOrder::lockForUpdate()
                ->where('seller_id', $request->user()->id)
                ->findOrFail($id);

            // Logic for when order is completed -> update seller balance/transactions can go here
            // For now, just update status
            $order->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated to ' . $request->status,
                'data' => $order
            ]);
        });
    }
}
