<?php

namespace App\Services\Payment\DTOs;

class PaymentIntentDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'INR',
        public readonly ?string $description = null,
        public readonly ?string $customerId = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerPhone = null,
        public readonly ?array $metadata = null,
        public readonly ?string $orderId = null,
    ) {}
}
