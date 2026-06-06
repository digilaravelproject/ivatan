<?php

namespace App\Services\Payment\DTOs;

class SubscriptionDTO
{
    public function __construct(
        public readonly string $gatewaySubscriptionId,
        public readonly string $status,
        public readonly ?string $gatewayPlanId = null,
        public readonly ?string $gatewayCustomerId = null,
        public readonly ?string $currentPeriodStart = null,
        public readonly ?string $currentPeriodEnd = null,
        public readonly ?string $cancelledAt = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = 'INR',
        public readonly ?array $rawResponse = null,
    ) {}
}
