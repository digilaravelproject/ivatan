<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Services\AdPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdPaymentController_old extends Controller
{
    /**
     * Get existing pending payment OR create a new Razorpay order if missing.
     */
    public function getPendingOrder(Ad $ad, AdPaymentService $paymentService)
    {
        $user = auth()->user();

        if ($ad->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($ad->status !== 'awaiting_payment') {
            return response()->json(['message' => 'Ad is not ready for payment'], 422);
        }

        // fetch existing pending payment
        $payment = $ad->payments()->where('status', 'pending')->latest()->get();

        if (! $payment) {
            // create new payment record
            $amount = $ad->package ? $ad->package->price : 0;

            $payment = AdPayment::create([
                'ad_id' => $ad->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $ad->package?->currency ?? 'INR',
                'status' => 'pending',
            ]);

            // create Razorpay order (amount in paise)
            $order = $paymentService->createOrder($payment, (int)round($amount * 100));

            // save Razorpay order ID
            $payment->update(['razorpay_order_id' => $order['id']]);
        } else {
            $order = [
                'id' => $payment->razorpay_order_id,
                'amount' => $payment->amount * 100,
                'currency' => $payment->currency,
            ];
        }

        return response()->json([
            'payment' => $payment,
            'razorpay_order' => $order,
        ]);
    }

    /**
     * Verify Razorpay payment
     */
    public function verify(Request $request, AdPaymentService $paymentService)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $payment = AdPayment::where('razorpay_order_id', $request->razorpay_order_id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment record not found'], 404);
        }

        try {
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
            $ad->start_at = Carbon::now();
            $duration = $ad->package?->duration_days ?? 7;
            $ad->end_at = Carbon::now()->addDays($duration);
            $ad->save();

            return response()->json(['success' => true, 'message' => 'Payment verified and ad is live', 'ad' => $ad]);
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed', 'meta' => ['error' => $e->getMessage()]]);
            return response()->json(['success' => false, 'message' => 'Payment verification failed', 'error' => $e->getMessage()], 422);
        }
    }
}
