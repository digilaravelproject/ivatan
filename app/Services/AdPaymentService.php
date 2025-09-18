<?php


namespace App\Services;


use App\Models\AdPayment;
use Razorpay\Api\Api;


class AdPaymentService
{
    protected Api $api;


    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }


    /**
     * Create a Razorpay order tied to the AdPayment record.
     * $amountInPaise integer (e.g. 10000 for ₹100)
     */
    public function createOrder(AdPayment $payment, int $amountInPaise): array
    {
        $orderData = [
            'receipt' => 'ad_' . $payment->id . '_' . time(),
            'amount' => $amountInPaise,
            'currency' => $payment->currency ?? 'INR',
            'payment_capture' => 1, // auto-capture
        ];


        $order = $this->api->order->create($orderData);


        return [
            'id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
        ];
    }


    /**
     * Verify razorpay payment signature - throws exception if invalid
     */
    public function verifyPaymentSignature(string $razorpayOrderId, string $razorpayPaymentId, string $razorpaySignature): bool
    {
        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ];


        // will throw \Razorpay\Api\Errors\SignatureVerificationError on invalid signature
        $this->api->utility->verifyPaymentSignature($attributes);


        return true;
    }
}
