<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserShipping;

class OrderController extends Controller
{
    /**
     * Display list of orders with optional status filter.
     */
    public function index(Request $request)
    {
        $orders = UserOrder::with(['buyer', 'items'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            // ->paginate(20);
            ->get();

        // return view('admin.orders.index', compact('orders'));
        return $orders;
    }

    /**
     * Update the status of an order and update/create shipping info.
     */
    public function updateStatus(Request $request, UserOrder $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,delivered,cancelled',
            'shipping_provider' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $order->status = $request->status;

        // Optional: update payment status based on order status
        if ($request->status === 'paid') {
            $order->payment_status = 'paid';
        } elseif ($request->status === 'cancelled') {
            $order->payment_status = 'refunded'; // Adjust as per business logic
        }

        $order->save();

        // Handle shipping info if provided
        if ($request->filled('shipping_provider') || $request->filled('tracking_number')) {
            $shipping = $order->shipping;

            if (!$shipping) {
                $shipping = new UserShipping([
                    'uuid' => Str::uuid(),
                    'order_id' => $order->id,
                ]);
            }

            $shipping->provider = $request->shipping_provider ?? $shipping->provider;
            $shipping->tracking_number = $request->tracking_number ?? $shipping->tracking_number;
            $shipping->status = $order->status === 'shipped' ? 'shipped' : ($shipping->status ?? 'pending');
            $shipping->save();
        }

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
