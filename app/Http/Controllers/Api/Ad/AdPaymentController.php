<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Services\Payment\PaymentOrchestrator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdPaymentController extends Controller
{
    public function __construct(
        protected PaymentOrchestrator $orchestrator,
    ) {}

    public function getPendingOrder(Ad $ad): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            if ($ad->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($ad->status !== 'awaiting_payment') {
                return response()->json(['success' => false, 'message' => 'Ad is not ready for payment'], 422);
            }

            $payment = $ad->payments()->where('status', 'pending')->latest()->first();

            if (!$payment) {
                if (!$ad->package) {
                    return response()->json(['success' => false, 'message' => 'Ad package not found'], 422);
                }

                $amount = (float) $ad->package->price;

                $payment = AdPayment::create([
                    'ad_id' => $ad->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => $ad->package->currency,
                    'status' => 'pending',
                ]);

                $result = $this->orchestrator->createAdPayment($payment, $ad, $user);

                return response()->json($result);
            }

            $activeGateway = $this->orchestrator->activeGateway();
            $order = [
                'id' => $payment->gateway_order_id ?? $payment->razorpay_order_id,
                'amount' => (int) round($payment->amount * 100),
                'currency' => $payment->currency,
            ];

            if ($activeGateway === 'razorpay') {
                $order['razorpay_order_id'] = $payment->razorpay_order_id;
            }

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'gateway_order' => $order,
                'razorpay_order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPendingOrder: ' . $e->getMessage(), [
                'ad_id' => $ad->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch or create payment order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required_without:merchantTransactionId|string',
            'razorpay_payment_id' => 'required_without:merchantTransactionId|string',
            'razorpay_signature' => 'required_without:merchantTransactionId|string',
            'merchantTransactionId' => 'required_without:razorpay_order_id|string',
            'transactionId' => 'nullable|string',
        ]);

        try {
            $activeGateway = $this->orchestrator->activeGateway();

            $gatewayOrderId = $request->input('merchantTransactionId')
                ?? $request->input('razorpay_order_id');

            $payment = AdPayment::where(function ($q) use ($gatewayOrderId) {
                $q->where('gateway_order_id', $gatewayOrderId)
                  ->orWhere('razorpay_order_id', $gatewayOrderId);
            })->first();

            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Payment record not found'], 404);
            }

            if ($payment->status === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already verified',
                    'ad' => $payment->ad,
                ]);
            }

            $payload = $request->all();
            if ($activeGateway === 'phonepe') {
                $payload = ['merchantTransactionId' => $gatewayOrderId];
            }

            $result = $this->orchestrator->verifyAdPayment($payment, $payload);

            if (!$result->success) {
                $payment->update([
                    'status' => 'failed',
                    'meta' => ['error' => $result->message],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed',
                    'error' => $result->message,
                ], 422);
            }

            $this->orchestrator->processAdPayment($payment, $payment->ad, $result);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and ad status updated',
                'ad' => $payment->ad,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
