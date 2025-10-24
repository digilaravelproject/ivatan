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


        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));



        $razorpayOrder = $api->order->create([
            'receipt' => (string) $order->uuid,
            'amount' => (int) ($order->total_amount * 100),
            'currency' => 'INR',
        ]);
        // Store Razorpay order ID in DB (so we can link it later)
        UserPayment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'gateway' => 'razorpay',
                'status' => 'initiated',
                'meta' => json_encode(['razorpay_order_id' => $razorpayOrder['id']]),
            ]
        );

        return response()->json([
            'success' => true,
            'razorpay_order_id' => $razorpayOrder['id'],
            'razorpay_key' => config('services.razorpay.key'),
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'Order ID' => $order->id,
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
            return response()->json([
                'success' => false,
                'message' => 'Order not found or payment already processed.',
            ], 404);
        }

        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));


        try {
            // Verify Razorpay signature
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay signature verification failed', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify payment. Please contact support if this persists.',
                'error' => $e->getMessage(),
            ], 422);
        }

        try {
            // Fetch payment details from Razorpay
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            // ✅ Edge case: Check if Razorpay payment amount matches our order total
            if ((int) $payment->amount / 100 !== (int) $order->total_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount mismatch detected.',
                ], 400);
            }


            // Handle different statuses
            if ($payment->status === 'authorized') {
                $payment = $payment->capture(['amount' => $payment->amount]);
            }

            if ($payment->status !== 'captured') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is not yet captured. Current status: ' . $payment->status,
                    'action' => 'Please try again or contact support.',
                ], 400);
            }

            // All good — dispatch async processing
            ProcessOrderPayment::dispatch(
                $order->id,
                $request->razorpay_payment_id,
                $request->razorpay_order_id,
                $request->razorpay_signature
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully. Order is being processed.',
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay payment fetch/capture failed', [
                'order_id' => $order->id,
                'payment_id' => $request->razorpay_payment_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Razorpay API error while fetching or capturing payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyRazorpayPayment_old(Request $request)
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

        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));


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
