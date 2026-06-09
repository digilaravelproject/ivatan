<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\DTOs\PaymentIntentDTO;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected string $key;
    protected string $secret;
    protected ?string $webhookSecret = null;
    protected string $baseUrl = 'https://api.razorpay.com/v1';

    protected ?\Razorpay\Api\Api $api = null;

    public function configure(array $config): void
    {
        $this->key = $config['key'] ?? '';
        $this->secret = $config['secret'] ?? '';
        $this->webhookSecret = $config['webhook_secret'] ?? null;

        if (!empty($this->key) && !empty($this->secret)) {
            $this->api = new \Razorpay\Api\Api($this->key, $this->secret);
        }
    }

    public function createPaymentIntent(PaymentIntentDTO $dto): PaymentResult
    {
        try {
            $orderData = [
                'amount' => (int) round($dto->amount * 100),
                'currency' => $dto->currency,
                'receipt' => $dto->orderId ?? uniqid('receipt_'),
            ];

            if ($dto->description) {
                $orderData['notes']['description'] = $dto->description;
            }

            if ($dto->metadata) {
                $orderData['notes'] = array_merge($orderData['notes'] ?? [], $dto->metadata);
            }

            $order = $this->api->order->create($orderData);

            return PaymentResult::success(
                gatewayOrderId: $order['id'],
                status: 'created',
                amount: $dto->amount,
                currency: $dto->currency,
                rawResponse: $order->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay: createPaymentIntent failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            throw PaymentGatewayException::gatewayError('razorpay', $e->getMessage(), (string) $e->getCode());
        }
    }

    public function verifyPayment(array $payload): PaymentResult
    {
        try {
            $razorpayOrderId = $payload['razorpay_order_id'] ?? '';
            $razorpayPaymentId = $payload['razorpay_payment_id'] ?? '';
            $razorpaySignature = $payload['razorpay_signature'] ?? '';

            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);

            $payment = $this->api->payment->fetch($razorpayPaymentId);

            return PaymentResult::success(
                gatewayOrderId: $razorpayOrderId,
                gatewayPaymentId: $razorpayPaymentId,
                status: 'paid',
                amount: $payment['amount'] / 100,
                currency: $payment['currency'],
                rawResponse: $payment->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay: verifyPayment failed', [
                'error' => $e->getMessage(),
            ]);
            return PaymentResult::failed('Payment verification failed: ' . $e->getMessage(), (string) $e->getCode());
        }
    }

    public function createSubscriptionPlan(string $name, float $amount, string $currency, string $interval, int $intervalCount = 1): PaymentResult
    {
        try {
            Log::info('Razorpay Key in createSubscriptionPlan: ' . $this->key);

            $planData = [
                'period' => $interval,
                'interval' => $intervalCount,
                'item' => [
                    'name' => $name,
                    'amount' => (int) round($amount * 100),
                    'currency' => strtoupper($currency),
                    'description' => "{$name} - {$intervalCount} {$interval}",
                ],
            ];

            // Use the official Razorpay SDK as requested
            $plan = $this->api->plan->create($planData);

            return PaymentResult::success(
                transactionId: $plan['id'],
                status: 'created',
                amount: $amount,
                currency: $currency,
                rawResponse: $plan->toArray(),
            );
        } catch (\Throwable $e) {
            Log::error('Razorpay: createSubscriptionPlan failed', [
                'error' => $e->getMessage(),
            ]);

            // Diagnostic call to get the exact raw API response from Razorpay
            try {
                $response = Http::withBasicAuth($this->key, $this->secret)
                    ->post("{$this->baseUrl}/plans", $planData);
                Log::error('Razorpay debug raw API response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Throwable $logEx) {
                Log::error('Razorpay diagnostic request failed', ['error' => $logEx->getMessage()]);
            }

            $errorMessage = $e->getMessage();
            
            // Translate the Razorpay SDK's internal "Array to string conversion" bug into a clear message
            if (str_contains($errorMessage, 'Array to string conversion')) {
                $errorMessage = 'Razorpay API returned an unauthorized or malformed response. Please verify that your active API Key and Secret in Settings are correct and active.';
            }

            throw PaymentGatewayException::gatewayError('razorpay', $errorMessage);
        }
    }

    public function createSubscription(string $customerId, string $planId, int $trialDays = 0): PaymentResult
    {
        try {
            $subscriptionData = [
                'plan_id' => $planId,
                'customer_notify' => 1,
                'total_count' => 100,
            ];

            if ($trialDays > 0) {
                $subscriptionData['start_at'] = now()->addDays($trialDays)->timestamp;
            }

            $subscription = $this->api->subscription->create($subscriptionData);

            return PaymentResult::success(
                gatewaySubscriptionId: $subscription['id'],
                status: $subscription['status'],
                rawResponse: $subscription->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay: createSubscription failed', [
                'error' => $e->getMessage(),
            ]);
            throw PaymentGatewayException::gatewayError('razorpay', $e->getMessage(), (string) $e->getCode());
        }
    }

    public function cancelSubscription(string $gatewaySubscriptionId, string $mode = 'end_of_period'): PaymentResult
    {
        try {
            $subscription = $this->api->subscription->fetch($gatewaySubscriptionId);

            $cancelAtEnd = $mode === 'end_of_period' ? true : false;

            $cancelled = $subscription->cancel($cancelAtEnd);

            return PaymentResult::success(
                gatewaySubscriptionId: $gatewaySubscriptionId,
                status: $cancelled['status'] ?? 'cancelled',
                rawResponse: $cancelled->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            Log::error('Razorpay: cancelSubscription failed', [
                'error' => $e->getMessage(),
                'subscription_id' => $gatewaySubscriptionId,
            ]);
            throw PaymentGatewayException::gatewayError('razorpay', "Failed to cancel subscription: {$e->getMessage()}", (string) $e->getCode());
        }
    }

    public function pauseSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        try {
            $subscription = $this->api->subscription->fetch($gatewaySubscriptionId);
            $paused = $subscription->pause();

            return PaymentResult::success(
                gatewaySubscriptionId: $gatewaySubscriptionId,
                status: $paused['status'] ?? 'paused',
                rawResponse: $paused->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            throw PaymentGatewayException::gatewayError('razorpay', "Failed to pause subscription: {$e->getMessage()}", (string) $e->getCode());
        }
    }

    public function resumeSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        try {
            $subscription = $this->api->subscription->fetch($gatewaySubscriptionId);
            $resumed = $subscription->resume();

            return PaymentResult::success(
                gatewaySubscriptionId: $gatewaySubscriptionId,
                status: $resumed['status'] ?? 'active',
                rawResponse: $resumed->toArray(),
            );
        } catch (\Razorpay\Api\Errors\Error $e) {
            throw PaymentGatewayException::gatewayError('razorpay', "Failed to resume subscription: {$e->getMessage()}", (string) $e->getCode());
        }
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    public function parseWebhookEvent(array $payload): string
    {
        return $payload['event'] ?? 'unknown';
    }

    public function testConnection(): bool
    {
        try {
            $response = Http::withBasicAuth($this->key, $this->secret)
                ->get("{$this->baseUrl}/payments", ['count' => 1]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Razorpay: Test connection failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Razorpay: Test connection exception', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
