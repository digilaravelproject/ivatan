<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ecommerce\UserOrder;

class OrderController extends Controller
{
    // List logged-in user's orders
    public function index(Request $request)
    {
        $orders = UserOrder::with('items', 'payment', 'shipping', 'address')
            ->where('buyer_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // Show single order (only if authorized by policy)
    public function show(Request $request, UserOrder $order)
    {
        $this->authorize('view', $order);

        $order->load('items', 'payment', 'shipping', 'buyer', 'address');

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    // Delete order (only pending & owned)
    public function destroy(Request $request, UserOrder $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }
}
