<?php

namespace App\Console\Commands;

use App\Services\Payment\GatewayManager;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Setting\SettingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PaymentHealthCheck extends Command
{
    protected $signature = 'payments:health-check';
    protected $description = 'Verify payment gateway configuration and connectivity';

    public function handle(SettingService $settings, GatewayManager $gatewayManager): int
    {
        $healthy = true;

        $this->info('Running payment gateway health check...');

        $activeGateway = $settings->get('payment.active_gateway', 'razorpay');
        $this->line(" Active gateway: {$activeGateway}");

        $webhookSecret = $settings->get('payment.razorpay.webhook_secret', '');
        if (empty($webhookSecret)) {
            $this->error(' Webhook secret is not configured!');
            Log::critical('PaymentHealthCheck: webhook secret missing');
            $healthy = false;
        } else {
            $this->info(' Webhook secret: configured');
        }

        $key = $settings->get('payment.razorpay.key', '');
        $secret = $settings->get('payment.razorpay.secret', '');

        if (empty($key) || empty($secret)) {
            $this->error(' API credentials are not configured!');
            Log::critical('PaymentHealthCheck: API credentials missing');
            $healthy = false;
        } else {
            $this->info(' API credentials: configured');

            try {
                $gateway = $gatewayManager->driver($activeGateway);
                $connected = $gateway->testConnection();
                if ($connected) {
                    $this->info(" Gateway connection: OK");
                } else {
                    $this->error(" Gateway connection: FAILED");
                    Log::error('PaymentHealthCheck: gateway connection test failed');
                    $healthy = false;
                }
            } catch (PaymentGatewayException $e) {
                $this->error(" Gateway error: {$e->getMessage()}");
                $healthy = false;
            }
        }

        if ($healthy) {
            $this->info(' Payment gateway health check: PASSED');
            return Command::SUCCESS;
        }

        $this->warn(' Payment gateway health check: FAILED — review issues above');
        return Command::FAILURE;
    }
}
