<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ecommerce\UserOrder;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerOrderController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                UserOrder::STATUS_ACCEPTED,
                UserOrder::STATUS_REJECTED,
                UserOrder::STATUS_PROCESSING,
                UserOrder::STATUS_PAID,
                UserOrder::STATUS_SHIPPED,
                UserOrder::STATUS_DELIVERED,
                UserOrder::STATUS_CANCELLED,
            ]),
        ]);

        $order = DB::transaction(function () use ($request, $id) {
            return UserOrder::lockForUpdate()
                ->where('seller_id', $request->user()->id)
                ->findOrFail($id);
        });

        $order->update(['status' => $request->status]);

        // Notify buyer after status update
        try {
            $buyer = User::find($order->buyer_id);
            if ($buyer) {
                $this->notificationService->sendToUser($buyer, 'order_status', [
                    'title'         => 'Order Updated',
                    'message'       => 'Your order #' . $order->id . ' status has been updated to ' . $request->status,
                    'order_id'      => $order->id,
                    'order_uuid'    => $order->uuid,
                    'new_status'    => $request->status,
                    'seller_name'   => $request->user()->name,
                    'action_url'    => null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Order status notification failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . $request->status,
            'data' => $order
        ]);
    }
}
