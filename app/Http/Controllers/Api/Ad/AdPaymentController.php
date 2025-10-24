<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Services\AdPaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdPaymentController extends Controller
{
    /**
     * Get existing pending payment OR create a new Razorpay order if missing.
     */
    public function getPendingOrder(Ad $ad, AdPaymentService $paymentService): JsonResponse
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            if ($ad->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($ad->status !== 'awaiting_payment') {
                return response()->json(['success' => false, 'message' => 'Ad is not ready for payment'], 422);
            }

            $payment = $ad->payments()->where('status', 'pending')->latest()->first();

            if (! $payment) {
                if (! $ad->package) {
                    return response()->json(['success' => false, 'message' => 'Ad package not found'], 422);
                }

                $amount = (float) $ad->package->price; // exact float

                $payment = AdPayment::create([
                    'ad_id' => $ad->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'currency' => $ad->package->currency,
                    'status' => 'pending',
                ]);

                // create Razorpay order using exact float converted to paise
                $amountInPaise = (int) round($amount * 100, 2); // Razorpay needs integer paise
                $order = $paymentService->createOrder($payment, $amountInPaise);

                $payment->update(['razorpay_order_id' => $order['id']]);
            } else {
                $order = [
                    'id' => $payment->razorpay_order_id,
                    'amount' => (int) round($payment->amount * 100, 2),
                    'currency' => $payment->currency,
                ];
            }

            return response()->json([
                'success' => true,
                'payment' => $payment,
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

    /**
     * Verify Razorpay payment safely with transactions and double-check
     */
    public function verify(Request $request, AdPaymentService $paymentService): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $payment = null;

        try {
            $payment = AdPayment::where('razorpay_order_id', $request->razorpay_order_id)->first();

            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Payment record not found'], 404);
            }

            if ($payment->status === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already verified',
                    'ad' => $payment->ad
                ]);
            }

            DB::transaction(function () use ($paymentService, $payment, $request) {

                // Verify Razorpay signature
                $paymentService->verifyPaymentSignature(
                    $request->razorpay_order_id,
                    $request->razorpay_payment_id,
                    $request->razorpay_signature
                );

                // Mark payment success
                $payment->update([
                    'status' => 'success',
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);

                // Activate ad based on schedule
                $ad = $payment->ad;
                $startAt = $ad->start_at ?? Carbon::now(); // If no start date, use now
                $duration = max(1, $ad->package?->duration_days ?? 7);
                $endAt = (clone $startAt)->addDays($duration);

                if ($startAt->isFuture()) {
                    // Start date is in future → approved
                    $ad->status = 'approved';
                } else {
                    // Start date is now or past → live
                    $ad->status = 'live';
                    $startAt = Carbon::now();
                    $endAt = (clone $startAt)->addDays($duration);
                }

                $ad->start_at = $startAt;
                $ad->end_at = $endAt;
                $ad->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and ad status updated',
                'ad' => $payment->ad,
            ]);
        } catch (\Exception $e) {
            if ($payment && $payment->status !== 'success') {
                $payment->update([
                    'status' => 'failed',
                    'meta' => ['error' => $e->getMessage()]
                ]);
            }

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

    public function verify_old(Request $request, AdPaymentService $paymentService): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $payment = null;

        try {
            $payment = AdPayment::where('razorpay_order_id', $request->razorpay_order_id)->first();

            if (! $payment) {
                return response()->json(['success' => false, 'message' => 'Payment record not found'], 404);
            }

            if ($payment->status === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already verified',
                    'ad' => $payment->ad
                ]);
            }

            DB::transaction(function () use ($paymentService, $payment, $request) {

                // Verify Razorpay signature
                $paymentService->verifyPaymentSignature(
                    $request->razorpay_order_id,
                    $request->razorpay_payment_id,
                    $request->razorpay_signature
                );

                // mark payment success
                $payment->update([
                    'status' => 'success',
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);

                // activate ad
                $ad = $payment->ad;
                $ad->status = 'live';
                $ad->start_at = Carbon::now(); // server timezone
                $duration = max(1, $ad->package?->duration_days ?? 7);
                $ad->end_at = Carbon::now()->addDays($duration);
                $ad->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and ad is live',
                'ad' => $payment->ad,
            ]);
        } catch (\Exception $e) {
            if ($payment && $payment->status !== 'success') {
                $payment->update([
                    'status' => 'failed',
                    'meta' => ['error' => $e->getMessage()]
                ]);
            }

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
