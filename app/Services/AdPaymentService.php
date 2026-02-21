<?php

namespace App\Services;

use App\Models\AdPayment;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class AdPaymentService
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Create a Razorpay order tied to the AdPayment record.
     * Accepts exact float amount and converts to paise internally
     *
     * @param AdPayment $payment
     * @param float $amount Exact float (e.g., 99.75)
     * @return array
     */
    public function createOrder(AdPayment $payment, float $amount): array
    {
        try {
            $amountInPaise = (int) round($amount * 100); // convert to paise for Razorpay

            $orderData = [
                'receipt' => 'ad_' . $payment->id . '_' . time(),
                'amount' => $amountInPaise,
                'currency' => $payment->currency ?? 'INR',
                'payment_capture' => 1,
            ];

            $order = $this->api->order->create($orderData);

            return [
                'id' => $order['id'],
                'amount' => $amountInPaise, // integer paise
                'currency' => $order['currency'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify Razorpay payment signature - throws exception if invalid
     */
    public function verifyPaymentSignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ];

        // will throw \Razorpay\Api\Errors\SignatureVerificationError if invalid
        $this->api->utility->verifyPaymentSignature($attributes);

        return true;
    }
}
