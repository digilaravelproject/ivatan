<?php

namespace App\Providers;

use App\Services\Payment\GatewayManager;
use App\Services\Payment\RazorpayGateway;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DynamicConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class, function () {
            return new SettingService();
        });

        $this->app->singleton(GatewayManager::class, function ($app) {
            $manager = new GatewayManager($app->make(SettingService::class));

            $manager->register('razorpay', RazorpayGateway::class);

            return $manager;
        });
    }

    public function boot(SettingService $settings): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        try {
            $activeGateway = $settings->get('payment.active_gateway', 'razorpay');

            config([
                'payment.active_gateway' => $activeGateway,
                'services.razorpay.key' => $settings->get('payment.razorpay.key'),
                'services.razorpay.secret' => $settings->get('payment.razorpay.secret'),
                'services.razorpay.webhook_secret' => $settings->get('payment.razorpay.webhook_secret'),
            ]);
        } catch (\Throwable $e) {
            Log::critical('DynamicConfigServiceProvider: Failed to load payment config from DB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
        }
    }
}
