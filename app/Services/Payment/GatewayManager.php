<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Log;

class GatewayManager
{
    protected array $gateways = [];

    protected ?string $defaultGateway = null;

    public function __construct(
        protected SettingService $settings,
    ) {
        $this->defaultGateway = $this->settings->get('payment.active_gateway', 'razorpay');
    }

    public function register(string $name, string $gatewayClass): void
    {
        $this->gateways[$name] = $gatewayClass;
    }

    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $name = $name ?? $this->defaultGateway;

        if (!isset($this->gateways[$name])) {
            throw PaymentGatewayException::configurationError(
                $name,
                "Gateway [{$name}] is not registered. Available: " . implode(', ', array_keys($this->gateways))
            );
        }

        $config = $this->settings->getGatewayConfig($name);

        if (empty($config['key']) || empty($config['secret'])) {
            throw PaymentGatewayException::configurationError(
                $name,
                'API credentials are not configured. Please update settings in the admin panel.'
            );
        }

        $gatewayClass = $this->gateways[$name];
        $gateway = app($gatewayClass);
        $gateway->configure($config);

        Log::debug("GatewayManager: Resolved gateway driver", ['gateway' => $name]);

        return $gateway;
    }

    public function getAvailableGateways(): array
    {
        return array_keys($this->gateways);
    }

    public function getDefaultGateway(): string
    {
        return $this->defaultGateway;
    }

    public function setDefaultGateway(string $name): void
    {
        $this->defaultGateway = $name;
    }
}
