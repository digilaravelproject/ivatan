<?php

namespace App\Services\Payment\DTOs;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId = null,
        public readonly ?string $gatewayOrderId = null,
        public readonly ?string $gatewayPaymentId = null,
        public readonly ?string $gatewaySubscriptionId = null,
        public readonly ?string $status = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $message = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $redirectUrl = null,
        public readonly ?array $rawResponse = null,
    ) {}

    public static function success(
        ?string $transactionId = null,
        ?string $gatewayOrderId = null,
        ?string $gatewayPaymentId = null,
        ?string $gatewaySubscriptionId = null,
        ?string $status = 'success',
        ?float $amount = null,
        ?string $currency = null,
        ?string $message = null,
        ?string $redirectUrl = null,
        ?array $rawResponse = null,
    ): self {
        return new self(
            success: true,
            transactionId: $transactionId,
            gatewayOrderId: $gatewayOrderId,
            gatewayPaymentId: $gatewayPaymentId,
            gatewaySubscriptionId: $gatewaySubscriptionId,
            status: $status,
            amount: $amount,
            currency: $currency,
            message: $message,
            redirectUrl: $redirectUrl,
            rawResponse: $rawResponse,
        );
    }

    public static function failed(
        string $message,
        ?string $errorCode = null,
        ?array $rawResponse = null,
    ): self {
        return new self(
            success: false,
            message: $message,
            errorCode: $errorCode,
            rawResponse: $rawResponse,
        );
    }
}
