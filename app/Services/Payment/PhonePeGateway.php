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

    protected string $clientId = '';
    protected string $clientSecret = '';
    protected string $clientVersion = '1';

    protected string $webhookUsername = '';
    protected string $webhookPassword = '';

    public function configure(array $config): void
    {
        $this->clientId = $config['key'] ?? '';
        $this->clientSecret = $config['secret'] ?? '';
        $this->clientVersion = $config['webhook_secret'] ?? '1';
        $this->webhookUsername = $config['webhook_username'] ?? '';
        $this->webhookPassword = $config['webhook_password'] ?? '';

        $this->merchantId = $config['key'] ?? '';
        
        $saltKey = $config['secret'] ?? '';
        if (!empty($saltKey) && base64_decode($saltKey, true) !== false) {
            $decoded = base64_decode($saltKey);
            if (preg_match('/^[a-f0-9\-]{36}$/i', $decoded)) {
                $saltKey = $decoded;
            }
        }
        $this->saltKey = $saltKey;

        // If clientId is a V2 client ID (e.g. M23NCDAG7VSKU_2604301424), extract merchantId
        if (strpos($this->merchantId, '_') !== false) {
            $parts = explode('_', $this->merchantId);
            $this->merchantId = $parts[0];
        }

        $this->saltIndex = $config['webhook_secret'] ?? '1';
        $this->env = $config['env'] ?? 'sandbox';

        $this->baseUrl = $this->env === 'production'
            ? 'https://api.phonepe.com/apis/pg-sandbox' // Default preprod host, but let's check
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }

    protected function getAccessToken(): ?string
    {
        if (strpos($this->clientId, '_') === false) {
            return null; // Not a V2 Client ID
        }

        $cacheKey = "phonepe_oauth_token_" . md5($this->clientId);
        $cachedToken = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($cachedToken) {
            return $cachedToken;
        }

        try {
            $authUrl = $this->env === 'production'
                ? 'https://api.phonepe.com/apis/identity-manager/v1/oauth/token'
                : 'https://api-preprod.phonepe.com/apis/pg-sandbox/v1/oauth/token';

            $response = Http::asForm()->post($authUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'client_version' => $this->clientVersion,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                if ($token) {
                    $expiresIn = $data['expires_in'] ?? 3600;
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $token, $expiresIn - 60);
                    return $token;
                }
            }
        } catch (\Throwable $e) {
            Log::error('PhonePe: failed to fetch OAuth token', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function createPaymentIntent(PaymentIntentDTO $dto): PaymentResult
    {
        try {
            $merchantTransactionId = $dto->orderId ?? uniqid('TXN_');

            // If we have V2 credentials, use standard checkout v2
            $token = $this->getAccessToken();
            if ($token) {
                $endpoint = '/checkout/v2/pay';
                
                $payload = [
                    'merchantOrderId' => $merchantTransactionId,
                    'amount' => (int) round($dto->amount * 100), // in paise
                    'expireAfter' => 3600,
                    'paymentFlow' => [
                        'type' => 'PG_CHECKOUT',
                        'merchantUrls' => [
                            'redirectUrl' => route('payment.callback', [
                                'gateway' => 'phonepe',
                                'code' => 'PAYMENT_SUCCESS',
                                'merchantTransactionId' => $merchantTransactionId
                            ]),
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'O-Bearer ' . $token,
                ])->post("{$this->baseUrl}{$endpoint}", $payload);

                if ($response->failed()) {
                    Log::error('PhonePe V2 Checkout request failed', [
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
                $redirectUrl = $data['redirectUrl'] ?? $data['redirect_url'] ?? null;
                $orderId = $data['orderId'] ?? $data['order_id'] ?? null;

                if ((($data['state'] ?? '') === 'PENDING' || ($data['success'] ?? false) === true) && $redirectUrl) {
                    return PaymentResult::success(
                        transactionId: $merchantTransactionId,
                        gatewayOrderId: $orderId ?? $merchantTransactionId,
                        status: 'created',
                        amount: $dto->amount,
                        currency: $dto->currency,
                        redirectUrl: $redirectUrl,
                        rawResponse: $data
                    );
                }

                return PaymentResult::failed(
                    $data['message'] ?? 'Failed to initialize payment with PhonePe.',
                    $data['code'] ?? $data['state'] ?? 'ERROR',
                    $data
                );
            }

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

            // If we have V2 credentials, use standard checkout v2 status check
            $token = $this->getAccessToken();
            if ($token) {
                $endpoint = "/checkout/v2/order/{$merchantTransactionId}/status";
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'O-Bearer ' . $token,
                ])->get("{$this->baseUrl}{$endpoint}");

                if ($response->failed()) {
                    Log::error('PhonePe V2 Status request failed', [
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
                if (($data['state'] ?? '') === 'COMPLETED') {
                    $phonepeTxnId = $data['paymentDetails'][0]['transactionId'] ?? null;
                    return PaymentResult::success(
                        transactionId: $merchantTransactionId,
                        gatewayOrderId: $data['orderId'] ?? $merchantTransactionId,
                        gatewayPaymentId: $phonepeTxnId,
                        status: 'paid',
                        amount: isset($data['amount']) ? ($data['amount'] / 100) : null,
                        currency: 'INR',
                        rawResponse: $data
                    );
                }

                return PaymentResult::failed(
                    $data['message'] ?? 'Payment status is not successful.',
                    $data['state'] ?? 'ERROR',
                    $data
                );
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
        $planId = 'plan_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name)) . '_' . uniqid();
        return PaymentResult::success(
            transactionId: $planId,
            status: 'created',
            amount: $amount,
            currency: $currency,
            rawResponse: ['plan_id' => $planId, 'name' => $name, 'amount' => $amount, 'interval' => $interval]
        );
    }

    public function createSubscription(string $customerId, string $planId, int $trialDays = 0): PaymentResult
    {
        try {
            $plan = \App\Models\SubscriptionPlan::where('gateway_plan_id', $planId)->first();
            if (!$plan) {
                return PaymentResult::failed("Subscription plan not found in database for ID: {$planId}");
            }

            $merchantSubscriptionId = 'SUB_' . uniqid();
            $merchantOrderId = 'ORD_' . uniqid();

            // Set mandate validity to 10 years in the future (epoch milliseconds)
            $expireAt = now()->addYears(10)->timestamp * 1000;

            $payload = [
                'merchantOrderId' => $merchantOrderId,
                'amount' => (int) round($plan->price * 100), // paise
                'paymentFlow' => [
                    'type' => 'SUBSCRIPTION_CHECKOUT_SETUP',
                    'merchantUrls' => [
                        'redirectUrl' => route('payment.callback', [
                            'gateway' => 'phonepe',
                            'code' => 'PAYMENT_SUCCESS',
                            'merchantTransactionId' => $merchantOrderId
                        ]),
                        'cancelRedirectUrl' => route('payment.callback', [
                            'gateway' => 'phonepe',
                            'code' => 'PAYMENT_CANCELLED',
                            'merchantTransactionId' => $merchantOrderId
                        ]),
                    ],
                    'subscriptionDetails' => [
                        'subscriptionType' => 'RECURRING',
                        'merchantSubscriptionId' => $merchantSubscriptionId,
                        'authWorkflowType' => 'TRANSACTION',
                        'amountType' => 'FIXED',
                        'maxAmount' => (int) round($plan->price * 100), // paise
                        'frequency' => 'ON_DEMAND',
                        'productType' => 'UPI_MANDATE',
                        'expireAt' => $expireAt,
                    ]
                ]
            ];

            $token = $this->getAccessToken();
            if ($token) {
                $endpoint = '/checkout/v2/pay';
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'O-Bearer ' . $token,
                ])->post("{$this->baseUrl}{$endpoint}", $payload);

                if ($response->failed()) {
                    Log::error('PhonePe V2 Subscription request failed', [
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
                $redirectUrl = $data['redirectUrl'] ?? $data['redirect_url'] ?? null;
                
                if ($response->successful() && $redirectUrl) {
                    // Convert relative URL if needed
                    if (!str_starts_with($redirectUrl, 'http')) {
                        $host = $this->env === 'production' 
                            ? 'https://mercury.phonepe.com' 
                            : 'https://mercury-uat.phonepe.com';
                        $redirectUrl = $host . '/' . ltrim($redirectUrl, './');
                    }

                    return PaymentResult::success(
                        gatewayOrderId: $data['orderId'] ?? $merchantOrderId,
                        gatewaySubscriptionId: $merchantSubscriptionId,
                        status: 'pending',
                        amount: $plan->price,
                        currency: $plan->currency,
                        redirectUrl: $redirectUrl,
                        rawResponse: $data
                    );
                }

                return PaymentResult::failed(
                    $data['message'] ?? 'Failed to initialize subscription with PhonePe.',
                    $data['code'] ?? $data['state'] ?? 'ERROR',
                    $data
                );
            }

            $jsonPayload = json_encode($payload);
            $base64Payload = base64_encode($jsonPayload);

            $endpoint = '/checkout/v2/pay';
            $xVerify = hash('sha256', $base64Payload . $endpoint . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
            ])->post("{$this->baseUrl}{$endpoint}", [
                'request' => $base64Payload,
            ]);

            if ($response->failed()) {
                Log::error('PhonePe createSubscription request failed', [
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
            
            if ($response->successful() && isset($data['redirectUrl'])) {
                $redirectUrl = $data['redirectUrl'];
                
                // Convert relative URL if needed
                if (!str_starts_with($redirectUrl, 'http')) {
                    $host = $this->env === 'production' 
                        ? 'https://mercury.phonepe.com' 
                        : 'https://mercury-uat.phonepe.com';
                    $redirectUrl = $host . '/' . ltrim($redirectUrl, './');
                }

                return PaymentResult::success(
                    gatewayOrderId: $merchantOrderId,
                    gatewaySubscriptionId: $merchantSubscriptionId,
                    status: 'pending',
                    amount: $plan->price,
                    currency: $plan->currency,
                    redirectUrl: $redirectUrl,
                    rawResponse: $data
                );
            }

            return PaymentResult::failed(
                $data['message'] ?? 'Failed to initialize subscription with PhonePe.',
                $data['code'] ?? 'ERROR',
                $data
            );
        } catch (\Throwable $e) {
            Log::error('PhonePe: createSubscription exception', [
                'error' => $e->getMessage(),
            ]);
            throw PaymentGatewayException::gatewayError('phonepe', $e->getMessage());
        }
    }

    public function cancelSubscription(string $gatewaySubscriptionId, string $mode = 'end_of_period'): PaymentResult
    {
        try {
            $endpoint = "/checkout/v2/subscriptions/{$gatewaySubscriptionId}/cancel";
            $xVerify = hash('sha256', $endpoint . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $xVerify,
            ])->post("{$this->baseUrl}{$endpoint}");

            if ($response->failed() && $response->status() !== 204) {
                Log::error('PhonePe cancelSubscription request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return PaymentResult::failed(
                    'PhonePe cancellation request failed: ' . ($response->json('message') ?? 'Unknown error'),
                    (string) $response->status(),
                    $response->json()
                );
            }

            return PaymentResult::success(
                gatewaySubscriptionId: $gatewaySubscriptionId,
                status: 'cancelled',
                rawResponse: $response->json() ?? ['status' => 'cancelled']
            );
        } catch (\Throwable $e) {
            Log::error('PhonePe: cancelSubscription exception', [
                'error' => $e->getMessage(),
            ]);
            return PaymentResult::failed('Subscription cancellation failed: ' . $e->getMessage());
        }
    }

    public function pauseSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        return PaymentResult::success(
            gatewaySubscriptionId: $gatewaySubscriptionId,
            status: 'paused'
        );
    }

    public function resumeSubscription(string $gatewaySubscriptionId): PaymentResult
    {
        return PaymentResult::success(
            gatewaySubscriptionId: $gatewaySubscriptionId,
            status: 'active'
        );
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        // Decode secret if base64 encoded Client Secret is provided
        if (!empty($secret) && base64_decode($secret, true) !== false) {
            $decoded = base64_decode($secret);
            if (preg_match('/^[a-f0-9\-]{36}$/i', $decoded)) {
                $secret = $decoded;
            }
        }

        $parts = explode('###', $signature);
        if (count($parts) !== 2) {
            return false;
        }

        $hash = $parts[0];
        $expectedHash = hash('sha256', $payload . $secret);

        return hash_equals($expectedHash, $hash);
    }

    public function verifyV2WebhookSignature(string $rawBody, string $signature, string $secret): bool
    {
        // If we have webhookUsername and webhookPassword configured:
        if (!empty($this->webhookUsername) && !empty($this->webhookPassword)) {
            $expected = hash('sha256', $this->webhookUsername . ':' . $this->webhookPassword);
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        // Decode secret if base64 encoded Client Secret is provided
        if (!empty($secret) && base64_decode($secret, true) !== false) {
            $decoded = base64_decode($secret);
            if (preg_match('/^[a-f0-9\-]{36}$/i', $decoded)) {
                $secret = $decoded;
            }
        }

        $expectedHash = hash('sha256', $rawBody . $secret);

        return hash_equals($expectedHash, $signature);
    }

    public function parseWebhookEvent(array $payload): string
    {
        $code = $payload['code'] ?? $payload['state'] ?? '';
        $type = $payload['paymentFlow']['type'] ?? '';

        if ($type === 'SUBSCRIPTION_CHECKOUT_SETUP' || isset($payload['paymentFlow']['merchantSubscriptionId'])) {
            return ($code === 'COMPLETED' || $code === 'PAYMENT_SUCCESS') ? 'subscription.charged' : 'payment.failed';
        }

        return ($code === 'PAYMENT_SUCCESS' || $code === 'COMPLETED') ? 'payment.success' : 'payment.failed';
    }

    public function testConnection(): bool
    {
        try {
            // For Checkout V2, fetching the OAuth token is the auth check
            $token = $this->getAccessToken();
            if ($token) {
                return true;
            }

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

            $code = $response->json('code');

            // If the request succeeds, OR returns a PhonePe-specific API error like PAYMENT_NOT_FOUND,
            // it means signature authorization has succeeded and the credentials are valid.
            if ($response->successful() || in_array($code, ['PAYMENT_NOT_FOUND', 'PAYMENT_ERROR', 'PAYMENT_SUCCESS'])) {
                return true;
            }

            // Also check if we received standard validation response but not authorization/configuration failure
            if ($response->status() === 400 && $code && !in_array($code, ['AUTHORIZATION_FAILED', 'KEY_NOT_CONFIGURED'])) {
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
