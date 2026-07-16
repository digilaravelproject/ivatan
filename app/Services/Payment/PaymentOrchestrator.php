<?php

namespace App\Services\Payment;

use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Models\User;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\DTOs\PaymentIntentDTO;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Setting\SettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentOrchestrator
{
    protected ?PaymentGatewayInterface $gatewayInstance = null;

    public function __construct(
        protected GatewayManager $gatewayManager,
        protected SettingService $settings,
    ) {}

    public function driver(): PaymentGatewayInterface
    {
        return $this->gatewayInstance ??= $this->gatewayManager->driver();
    }

    public function activeGateway(): string
    {
        return $this->gatewayManager->getDefaultGateway();
    }

    public function resetDriver(): void
    {
        $this->gatewayInstance = null;
    }

    public function createEcommercePayment(UserOrder $order, User $user, string $ip, string $userAgent): array
    {
        $gateway = $this->driver();
        $activeGateway = $this->activeGateway();

        $dto = new PaymentIntentDTO(
            amount: (float) $order->total_amount,
            currency: 'INR',
            orderId: (string) $order->uuid,
            customerPhone: $user->phone ?? '',
        );

        $result = $gateway->createPaymentIntent($dto);

        if (!$result->success) {
            throw PaymentGatewayException::gatewayError($activeGateway, $result->message ?? 'Failed to create payment intent on gateway.');
        }

        UserPayment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'gateway' => $activeGateway,
                'status' => 'initiated',
                'meta' => [
                    'gateway_order_id' => $result->gatewayOrderId,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'redirect_url' => $result->redirectUrl,
                ],
            ]
        );

        return $this->buildCreateResponse($result, $order);
    }

    public function verifyEcommercePayment(UserOrder $order, array $gatewayPayload): PaymentResult
    {
        $gateway = $this->driver();

        $result = $gateway->verifyPayment($gatewayPayload);

        if (!$result->success) {
            return $result;
        }

        if (isset($result->amount) && (int) round($result->amount * 100) !== (int) round($order->total_amount * 100)) {
            return PaymentResult::failed('Payment amount mismatch detected.');
        }

        $paymentRecord = UserPayment::where('order_id', $order->id)->first();
        if ($paymentRecord && isset($paymentRecord->meta['gateway_order_id'])) {
            $actualId = $result->gatewayOrderId ?? '';
            $txnId = $result->transactionId ?? '';
            if ($paymentRecord->meta['gateway_order_id'] !== $actualId && $paymentRecord->meta['gateway_order_id'] !== $txnId) {
                return PaymentResult::failed('Gateway Order ID mismatch.');
            }
        }

        return $result;
    }

    public function processEcommercePayment(UserOrder $order, PaymentResult $result, array $gatewayPayload): void
    {
        $activeGateway = $this->activeGateway();

        \App\Jobs\ProcessOrderPayment::dispatch(
            $order->id,
            $result->gatewayPaymentId ?? $result->transactionId ?? $order->uuid,
            $result->gatewayOrderId ?? '',
            $result->rawResponse['checksum'] ?? '',
            $activeGateway,
        );
    }

    public function createAdPayment(AdPayment $payment, Ad $ad, User $user): array
    {
        $gateway = $this->driver();
        $activeGateway = $this->activeGateway();

        $dto = new PaymentIntentDTO(
            amount: (float) $payment->amount,
            currency: $payment->currency ?? 'INR',
            orderId: 'ad_' . $payment->id . '_' . time(),
            customerPhone: $user->phone ?? '',
        );

        $result = $gateway->createPaymentIntent($dto);

        if (!$result->success) {
            throw PaymentGatewayException::gatewayError($activeGateway, $result->message ?? 'Failed to create payment intent on gateway.');
        }

        $payment->update([
            'gateway' => $activeGateway,
            'gateway_order_id' => $result->gatewayOrderId,
        ]);

        $order = [
            'id' => $result->gatewayOrderId,
            'amount' => (int) round($payment->amount * 100),
            'currency' => $payment->currency,
        ];

        if ($activeGateway === 'razorpay') {
            $order['razorpay_order_id'] = $result->gatewayOrderId;
        }

        return [
            'success' => true,
            'payment' => $payment,
            'gateway_order' => $order,
            'razorpay_order' => $order,
        ];
    }

    public function verifyAdPayment(AdPayment $payment, array $gatewayPayload): PaymentResult
    {
        $gateway = $this->driver();
        return $gateway->verifyPayment($gatewayPayload);
    }

    public function processAdPayment(AdPayment $payment, Ad $ad, PaymentResult $result): void
    {
        DB::transaction(function () use ($payment, $ad, $result) {
            $payment->update([
                'status' => 'success',
                'gateway_payment_id' => $result->gatewayPaymentId ?? $result->transactionId,
            ]);

            $startAt = $ad->start_at ?? Carbon::now();
            $duration = max(1, $ad->package?->duration_days ?? 7);
            $endAt = (clone $startAt)->addDays($duration);

            if ($startAt->isFuture()) {
                $ad->status = 'approved';
            } else {
                $ad->status = 'live';
                $startAt = Carbon::now();
                $endAt = (clone $startAt)->addDays($duration);
            }

            $ad->start_at = $startAt;
            $ad->end_at = $endAt;
            $ad->save();
        });
    }

    public function handleWebhookEvent(string $gatewayName, string $event, array $payload): void
    {
        Log::info('PaymentOrchestrator: handling webhook event', [
            'gateway' => $gatewayName,
            'event' => $event,
        ]);
    }

    protected function buildCreateResponse(PaymentResult $result, UserOrder $order): array
    {
        $activeGateway = $this->activeGateway();

        $response = [
            'success' => true,
            'gateway' => $activeGateway,
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'order_id' => $order->id,
        ];

        if ($activeGateway === 'razorpay') {
            $response['razorpay_order_id'] = $result->gatewayOrderId;
            $response['razorpay_key'] = config('services.razorpay.key');
        } elseif ($activeGateway === 'phonepe') {
            $response['redirect_url'] = $result->redirectUrl;
            $response['merchant_transaction_id'] = $result->gatewayOrderId;
        }

        return $response;
    }

    public function createExclusiveContentPayment(\App\Models\ExclusiveContentPurchase $purchase, User $user): array
    {
        $gateway = $this->driver();
        $activeGateway = $this->activeGateway();

        $dto = new PaymentIntentDTO(
            amount: (float) $purchase->final_paid_amount,
            currency: 'INR',
            orderId: 'exc_' . $purchase->uuid,
            customerPhone: $user->phone ?? '',
        );

        $result = $gateway->createPaymentIntent($dto);

        if (!$result->success) {
            throw PaymentGatewayException::gatewayError($activeGateway, $result->message ?? 'Failed to create payment intent.');
        }

        $purchase->update([
            'gateway' => $activeGateway,
            'gateway_transaction_id' => $result->gatewayOrderId, // Store order ID first
        ]);

        $order = [
            'id' => $result->gatewayOrderId,
            'amount' => (int) round($purchase->final_paid_amount * 100),
            'currency' => 'INR',
        ];

        if ($activeGateway === 'razorpay') {
            $order['razorpay_order_id'] = $result->gatewayOrderId;
        }

        $response = [
            'success' => true,
            'purchase' => $purchase,
            'gateway_order' => $order,
        ];

        if ($activeGateway === 'phonepe') {
            if ($result->redirectUrl) {
                $response['redirect_url'] = $result->redirectUrl;
            }
        }

        return $response;
    }

    public function verifyExclusiveContentPayment(\App\Models\ExclusiveContentPurchase $purchase, array $gatewayPayload): PaymentResult
    {
        $gateway = $this->driver();
        return $gateway->verifyPayment($gatewayPayload);
    }

    public function createEnablementPayment(\App\Models\ExclusiveContentEnablement $enablement, User $user): array
    {
        $gateway = $this->driver();
        $activeGateway = $this->activeGateway();

        $dto = new PaymentIntentDTO(
            amount: (float) $enablement->fee_paid,
            currency: 'INR',
            orderId: 'ene_' . $enablement->id . '_' . time(),
            customerPhone: $user->phone ?? '',
        );

        $result = $gateway->createPaymentIntent($dto);

        if (!$result->success) {
            throw PaymentGatewayException::gatewayError($activeGateway, $result->message ?? 'Failed to create payment intent.');
        }

        $enablement->update([
            'gateway' => $activeGateway,
            'gateway_transaction_id' => $result->gatewayOrderId,
            'payment_status' => 'pending',
        ]);

        $order = [
            'id' => $result->gatewayOrderId,
            'amount' => (int) round($enablement->fee_paid * 100),
            'currency' => 'INR',
        ];

        if ($activeGateway === 'razorpay') {
            $order['razorpay_order_id'] = $result->gatewayOrderId;
        }

        $response = [
            'success' => true,
            'enablement' => $enablement,
            'gateway_order' => $order,
        ];

        if ($activeGateway === 'phonepe') {
            if ($result->redirectUrl) {
                $response['redirect_url'] = $result->redirectUrl;
            }
        }

        return $response;
    }

    public function verifyEnablementPayment(\App\Models\ExclusiveContentEnablement $enablement, array $gatewayPayload): PaymentResult
    {
        $gateway = $this->driver();
        return $gateway->verifyPayment($gatewayPayload);
    }
}
