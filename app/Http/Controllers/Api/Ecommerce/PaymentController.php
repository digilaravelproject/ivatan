<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessOrderPayment;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    // Create Razorpay order for existing order
    public function createRazorpayOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:user_orders,id',
        ]);

        $user = $request->user();

        $order = UserOrder::where('id', $request->order_id)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or already paid.'], 404);
        }
// dd(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));


       $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));


        $razorpayOrder = $api->order->create([
            'receipt' => (string) $order->uuid,
            'amount' => (int) ($order->total_amount * 100),
            'currency' => 'INR',
        ]);

        return response()->json([
            'success' => true,
            'razorpay_order_id' => $razorpayOrder['id'],
            'razorpay_key' => env('RAZORPAY_KEY'),
            'amount' => $order->total_amount,
            'currency' => 'INR',
        ]);
    }

    // Verify Razorpay payment and dispatch job
    public function verifyRazorpayPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'order_id' => 'required|exists:user_orders,id',
        ]);

        $user = $request->user();

        $order = UserOrder::where('id', $request->order_id)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or payment already verified.'], 404);
        }

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay payment verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.',
                'error' => $e->getMessage(),
            ], 422);
        }

        // Dispatch async job to process payment and order
        ProcessOrderPayment::dispatch($order->id, $request->razorpay_payment_id, $request->razorpay_order_id, $request->razorpay_signature);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified. Order processing started.',
            'order_id' => $order->id,
        ]);
    }
}
