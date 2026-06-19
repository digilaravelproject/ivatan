<?php

namespace App\Services\Payment\Contracts;

use App\Services\Payment\DTOs\PaymentIntentDTO;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\DTOs\SubscriptionDTO;

interface PaymentGatewayInterface
{
    public function configure(array $config): void;

    public function createPaymentIntent(PaymentIntentDTO $dto): PaymentResult;

    public function verifyPayment(array $payload): PaymentResult;

    public function createSubscriptionPlan(string $name, float $amount, string $currency, string $interval, int $intervalCount = 1): PaymentResult;

    public function createSubscription(string $customerId, string $planId, int $trialDays = 0): PaymentResult;

    public function cancelSubscription(string $gatewaySubscriptionId, string $mode = 'end_of_period'): PaymentResult;

    public function pauseSubscription(string $gatewaySubscriptionId): PaymentResult;

    public function resumeSubscription(string $gatewaySubscriptionId): PaymentResult;

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool;

    public function parseWebhookEvent(array $payload): string;

    public function testConnection(): bool;

    public function sendPreDebitNotification(string $gatewaySubscriptionId, float $amount, string $merchantUserId, string $preDebitTxnId): PaymentResult;

    public function chargeSubscription(string $gatewaySubscriptionId, float $amount, string $merchantUserId, string $debitTxnId): PaymentResult;
}
