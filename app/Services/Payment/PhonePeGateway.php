<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\DTOs\PaymentIntentDTO;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhonePeGateway implements PaymentGatewayInterface
{
    protected string $merchantId;
    protected string $saltKey;
    protected string $saltIndex;
    protected string $baseUrl;
    protected string $env;

    public function configure(array $config): void
    {
        $this->merchantId = $config['key'] ?? '';
        $this->saltKey = $config['secret'] ?? '';
        $this->saltIndex = $config['webhook_secret'] ?? '1';
        $this->env = $config['env'] ?? 'sandbox';

        $this->baseUrl = $this->env === 'production'
            ? 'https://api.phonepe.com/apis/hermes'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    public function createPaymentIntent(PaymentIntentDTO $dto): PaymentResult
    {
        try {
            $merchantTransactionId = $dto->orderId ?? uniqid('TXN_');
            $payload = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantTransactionId,
                'amount' => (int) round($dto->amount * 100), // in paise
                'redirectUrl' => route('payment.callback', ['gateway' => 'phonepe']),
                'redirectMode' => 'POST',
                'callbackUrl' => route('webhook.phonepe'),
                'mobileNumber' => $dto->customerPhone ?? '',
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE',
                ],
            ];

            if ($dto->customerId) {
                $payload['merchantUserId'] = $dto->customerId;
            } else {
                $payload['merchantUserId'] = 'MUID_' . uniqid();
            }

            $jsonPayload = json_encode($payload);
            $base64Payload = base64_encode($jsonPayload);

            $endpoint = '/pg/v1/pay';
            $xVerify = hash('sha256', $base64Payload . $endpoint . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
            ])->post("{$this->baseUrl}{$endpoint}", [
                'request' => $base64Payload,
            ]);

            if ($response->failed()) {
                Log::error('PhonePe createPaymentIntent request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return PaymentResult::failed(
                    'PhonePe API Request Failed: ' . ($response->json('message') ?? 'Unknown error'),
                    (string) $response->status(),
                    $response->json()
                );
            }

            $data = $response->json();
            if (($data['success'] ?? false) && isset($data['data']['instrumentResponse']['redirectInfo']['url'])) {
                return PaymentResult::success(
                    transactionId: $merchantTransactionId,
                    gatewayOrderId: $merchantTransactionId,
                    status: 'created',
                    amount: $dto->amount,
                    currency: $dto->currency,
                    redirectUrl: $data['data']['instrumentResponse']['redirectInfo']['url'],
                    rawResponse: $data
                );
            }

            return PaymentResult::failed(
                $data['message'] ?? 'Failed to initialize payment with PhonePe.',
                $data['code'] ?? 'ERROR',
                $data
            );
        } catch (\Throwable $e) {
            Log::error('PhonePe: createPaymentIntent exception', [
                'error' => $e->getMessage(),
            ]);
            throw PaymentGatewayException::gatewayError('phonepe', $e->getMessage());
        }
    }

    public function verifyPayment(array $payload): PaymentResult
    {
        try {
            $merchantTransactionId = $payload['merchantTransactionId'] ?? $payload['transactionId'] ?? '';
            if (empty($merchantTransactionId)) {
                return PaymentResult::failed('Missing transaction ID for verification.');
            }

            $endpoint = "/pg/v1/status/{$this->merchantId}/{$merchantTransactionId}";
            $xVerify = hash('sha256', $endpoint . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
                'X-MERCHANT-ID' => $this->merchantId,
            ])->get("{$this->baseUrl}{$endpoint}");

            if ($response->failed()) {
                Log::error('PhonePe verifyPayment request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return PaymentResult::failed(
                    'PhonePe verification request failed: ' . ($response->json('message') ?? 'Unknown error'),
                    (string) $response->status(),
                    $response->json()
                );
            }

            $data = $response->json();
            if (($data['success'] ?? false) && ($data['code'] ?? '') === 'PAYMENT_SUCCESS') {
                $phonepeTxnId = $data['data']['transactionId'] ?? null;
                return PaymentResult::success(
                    transactionId: $merchantTransactionId,
                    gatewayOrderId: $merchantTransactionId,
                    gatewayPaymentId: $phonepeTxnId,
                    status: 'paid',
                    amount: isset($data['data']['amount']) ? ($data['data']['amount'] / 100) : null,
                    currency: 'INR',
                    rawResponse: $data
                );
            }

            return PaymentResult::failed(
                $data['message'] ?? 'Payment status is not successful.',
                $data['code'] ?? 'ERROR',
                $data
            );
        } catch (\Throwable $e) {
            Log::error('PhonePe: verifyPayment exception', [
                'error' => $e->getMessage(),
            ]);
            return PaymentResult::failed('Payment verification failed: ' . $e->getMessage());
        }
    }

    public function createSubscriptionPlan(string $name, float $amount, string $currency, string $interval, int $intervalCount = 1): PaymentResult
    {
        throw PaymentGatewayException::gatewayError('phonepe', 'Subscriptions are not supported on PhonePe gateway.');
    }

    public function createSubscription(string $customerId, string $planId, int $trialDays = 0): PaymentResult
    {
        throw PaymentGatewayException::gatewayError('phonepe', 'Subscriptions are not supported on PhonePe gateway.');
    }

    public function cancelSubscription(string $gatewaySubscriptionId, string $mode = 'end_of_period'): PaymentResult
    {
        throw PaymentGatewayException::gatewayError('phonepe', 'Subscriptions are not supported on PhonePe gateway.');
    }

    public function pauseSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        throw PaymentGatewayException::gatewayError('phonepe', 'Subscriptions are not supported on PhonePe gateway.');
    }

    public function resumeSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        throw PaymentGatewayException::gatewayError('phonepe', 'Subscriptions are not supported on PhonePe gateway.');
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        // $payload is the raw body string, $signature is the X-VERIFY header value, $secret is the saltKey.
        // Format of signature: sha256(payload + saltKey) + "###" + saltIndex
        $parts = explode('###', $signature);
        if (count($parts) !== 2) {
            return false;
        }

        $hash = $parts[0];
        $expectedHash = hash('sha256', $payload . $secret);

        return hash_equals($expectedHash, $hash);
    }

    public function parseWebhookEvent(array $payload): string
    {
        return ($payload['code'] ?? '') === 'PAYMENT_SUCCESS' ? 'payment.success' : 'payment.failed';
    }

    public function testConnection(): bool
    {
        try {
            // PhonePe preprod/sandbox status endpoint doesn't support basic requests without a transaction,
            // but we can query standard status with a fake transaction to test salt verification correctness.
            $fakeTxn = 'CONN_TEST_' . uniqid();
            $endpoint = "/pg/v1/status/{$this->merchantId}/{$fakeTxn}";
            $xVerify = hash('sha256', $endpoint . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
                'X-MERCHANT-ID' => $this->merchantId,
            ])->get("{$this->baseUrl}{$endpoint}");

            if ($response->successful() && ($response->json('code') ?? '') === 'PAYMENT_ERROR') {
                return true;
            }

            if ($response->successful()) {
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('PhonePe: testConnection exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
