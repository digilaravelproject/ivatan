<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create Razorpay order/intent for existing order
     */
    public function createRazorpayOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:user_orders,id',
        ]);

        try {
            $user = $request->user();
            $ip = $request->ip();
            $userAgent = $request->header('User-Agent');

            $result = $this->paymentService->createRazorpayOrder($request->order_id, $user, $ip, $userAgent);

            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Razorpay order creation failed', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
            ]);

            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Verify payment and process order
     */
    public function verifyRazorpayPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'order_id' => 'required|exists:user_orders,id',
        ]);

        try {
            $user = $request->user();
            $orderId = $this->paymentService->verifyRazorpayPayment($user, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully. Order is being processed.',
                'order_id' => $orderId,
            ]);
        } catch (Exception $e) {
            Log::error('Payment verification failed', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
            ]);

            $status = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.',
                'error' => $e->getMessage(),
            ], $status);
        }
    }
}
