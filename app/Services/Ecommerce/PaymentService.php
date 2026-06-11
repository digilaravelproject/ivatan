<?php

namespace App\Services\Ecommerce;

use App\Services\Payment\GatewayManager;
use App\Services\Payment\DTOs\PaymentIntentDTO;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use Illuminate\Validation\ValidationException;
use Exception;

class PaymentService
{
    protected GatewayManager $gatewayManager;

    public function __construct(GatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Create Razorpay order/intent for existing order using the dynamic gateway settings
     */
    public function createRazorpayOrder($orderId, $user, string $ip, string $userAgent)
    {
        $order = UserOrder::where('id', $orderId)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            throw new Exception('Order not found or already paid.', 404);
        }

        // Use GatewayManager to get active driver dynamically configured in settings
        $gateway = $this->gatewayManager->driver();

        $dto = new PaymentIntentDTO(
            amount: (float) $order->total_amount,
            currency: 'INR',
            orderId: (string) $order->uuid
        );

        $result = $gateway->createPaymentIntent($dto);

        if (!$result->success) {
            throw new Exception($result->message ?? 'Failed to create payment intent on gateway.');
        }

        // Store Razorpay order ID in DB
        UserPayment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'gateway' => $this->gatewayManager->getDefaultGateway(),
                'status' => 'initiated',
                'meta' => [
                    'razorpay_order_id' => $result->gatewayOrderId,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                ],
            ]
        );

        return [
            'success' => true,
            'razorpay_order_id' => $result->gatewayOrderId,
            'razorpay_key' => config('services.razorpay.key'), // dynamically configured by DynamicConfigServiceProvider
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'Order ID' => $order->id,
        ];
    }

    /**
     * Verify payment and dispatch processing
     */
    public function verifyRazorpayPayment($user, array $data)
    {
        $order = UserOrder::where('id', $data['order_id'])
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            throw new Exception('Order not found or payment already processed.', 404);
        }

        // Use GatewayManager to get active driver dynamically
        $gateway = $this->gatewayManager->driver();

        $result = $gateway->verifyPayment($data);

        if (!$result->success) {
            throw new Exception($result->message ?? 'Payment verification failed.', 422);
        }

        // Check if amount matches order total
        if (isset($result->amount) && (int) round($result->amount * 100) !== (int) round($order->total_amount * 100)) {
            throw new Exception('Payment amount mismatch detected.', 400);
        }

        // Verify Razorpay Order ID matches our database record
        $paymentRecord = UserPayment::where('order_id', $order->id)->first();
        if ($paymentRecord && isset($paymentRecord->meta['razorpay_order_id'])) {
            if ($paymentRecord->meta['razorpay_order_id'] !== $data['razorpay_order_id']) {
                throw new Exception('Razorpay Order ID mismatch.', 400);
            }
        }

        // Dispatch async processing
        \App\Jobs\ProcessOrderPayment::dispatch(
            $order->id,
            $data['razorpay_payment_id'],
            $data['razorpay_order_id'],
            $data['razorpay_signature']
        );

        return $order->id;
    }
}
