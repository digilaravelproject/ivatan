<?php

namespace App\Services\Payment\Exceptions;

class PaymentGatewayException extends \RuntimeException
{
    public function __construct(
        string $message = 'Payment gateway error occurred.',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?string $gateway = null,
        public readonly ?string $errorCode = null,
        public readonly ?array $rawResponse = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function gatewayError(string $gateway, string $message, ?string $errorCode = null, ?array $rawResponse = null): self
    {
        return new self(
            message: "[{$gateway}] {$message}",
            code: 502,
            gateway: $gateway,
            errorCode: $errorCode,
            rawResponse: $rawResponse,
        );
    }

    public static function configurationError(string $gateway, string $message): self
    {
        return new self(
            message: "[{$gateway}] Configuration error: {$message}",
            code: 500,
            gateway: $gateway,
        );
    }

    public static function webhookError(string $gateway, string $message): self
    {
        return new self(
            message: "[{$gateway}] Webhook error: {$message}",
            code: 400,
            gateway: $gateway,
        );
    }
}
