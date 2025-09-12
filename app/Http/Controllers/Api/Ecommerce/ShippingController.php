<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserShipping;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    // Update shipping info (tracking number, status) - Admin or shipping partner use
    public function updateShipping(Request $request, $orderId)
    {
        $request->validate([
            'provider' => 'nullable|string|max:100',   // e.g. 'delhivery'
            'tracking_number' => 'required|string|max:255',
            'status' => 'required|string|in:pending,shipped,in_transit,out_for_delivery,delivered,cancelled',
            'meta' => 'nullable|array',
        ]);

        $order = UserOrder::findOrFail($orderId);

        $shipping = UserShipping::firstOrCreate(
            ['order_id' => $order->id],
            [
                'uuid' => (string) Str::uuid(),
                'status' => 'pending',
                'provider' => null,
                'tracking_number' => null,
                'meta' => null,
            ]
        );

        $shipping->provider = $request->input('provider', $shipping->provider);
        $shipping->tracking_number = $request->input('tracking_number');
        $shipping->status = $request->input('status');
        $shipping->meta = $request->input('meta') ? json_encode($request->input('meta')) : $shipping->meta;
        $shipping->save();

        // TODO: Notify user by email, SMS, etc. (separate notification system)

        return response()->json([
            'success' => true,
            'message' => 'Shipping info updated.',
            'shipping' => $shipping,
        ]);
    }

    // Get shipping info by order for user
    public function getShipping(Request $request, $orderId)
    {
        $order = UserOrder::where('id', $orderId)
            ->where('buyer_id', $request->user()->id)
            ->firstOrFail();

        $shipping = UserShipping::where('order_id', $order->id)->first();

        if (!$shipping) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping information not available yet.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'shipping' => $shipping,
        ]);
    }
}
