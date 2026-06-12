<?php

namespace App\Services\Ecommerce;

use App\Models\Ecommerce\UserOrder;
use App\Services\Payment\PaymentOrchestrator;
use Exception;

class PaymentService
{
    public function __construct(
        protected PaymentOrchestrator $orchestrator,
    ) {}

    public function createRazorpayOrder($orderId, $user, string $ip, string $userAgent)
    {
        $order = UserOrder::where('id', $orderId)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            throw new Exception('Order not found or already paid.', 404);
        }

        return $this->orchestrator->createEcommercePayment($order, $user, $ip, $userAgent);
    }

    public function verifyRazorpayPayment($user, array $data)
    {
        $orderId = $data['order_id'] ?? null;
        $order = UserOrder::where('id', $orderId)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'initiated')
            ->first();

        if (!$order) {
            throw new Exception('Order not found or payment already processed.', 404);
        }

        $activeGateway = $this->orchestrator->activeGateway();

        $payload = $data;
        if ($activeGateway === 'phonepe') {
            $payload = [
                'merchantTransactionId' => $data['merchantTransactionId'] ?? $data['transactionId'] ?? (string) $order->uuid,
            ];
        }

        $result = $this->orchestrator->verifyEcommercePayment($order, $payload);

        if (!$result->success) {
            throw new Exception($result->message ?? 'Payment verification failed.', 422);
        }

        $this->orchestrator->processEcommercePayment($order, $result, $payload);

        return $order->id;
    }
}
