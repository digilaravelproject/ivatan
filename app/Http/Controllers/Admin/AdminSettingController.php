<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Setting\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function __construct(
        protected SettingService $settings
    ) {}

    public function index(): View
    {
        $settings = [
            'payment_active_gateway' => $this->settings->get('payment.active_gateway', 'razorpay'),
            'services_razorpay_key' => $this->settings->get('payment.razorpay.key', ''),
            'services_razorpay_secret' => $this->settings->get('payment.razorpay.secret', ''),
            'services_razorpay_webhook_secret' => $this->settings->get('payment.razorpay.webhook_secret', ''),
            'services_phonepe_merchant_id' => $this->settings->get('payment.phonepe.key', ''),
            'services_phonepe_salt_key' => $this->settings->get('payment.phonepe.secret', ''),
            'services_phonepe_salt_index' => $this->settings->get('payment.phonepe.webhook_secret', '1'),
            'services_phonepe_env' => $this->settings->get('payment.phonepe.env', 'sandbox'),
            'subscription_default_duration' => $this->settings->get('subscription.default_duration', 30),
            'subscription_trial_days' => $this->settings->get('subscription.trial_days', 0),
            'subscription_cancellation_mode' => $this->settings->get('subscription.cancel_mode', 'end_of_period'),
            'subscription_auto_renew' => $this->settings->get('subscription.auto_renew', '1'),
            'subscription_invoice_footer' => $this->settings->get('subscription.invoice_footer', 'Thank you for your business.'),
            'subscription_grace_period' => $this->settings->get('subscription.grace_period', 7),
            'app_name' => $this->settings->get('general.site_name', config('app.name')),
            'admin_email' => $this->settings->get('general.admin_email', ''),
            'app_currency' => $this->settings->get('general.site_currency', 'INR'),
            'app_currency_symbol' => $this->settings->get('general.site_currency_symbol', '₹'),
            'profile_approval_required' => $this->settings->get('general.profile_approval_required', '1'),
            'default_profile_type' => $this->settings->get('general.default_profile_type', 'personal'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_active_gateway' => 'required|string|in:razorpay,phonepe',
            'services_razorpay_key' => 'required_if:payment_active_gateway,razorpay|nullable|string|max:255',
            'services_razorpay_secret' => 'required_if:payment_active_gateway,razorpay|nullable|string|max:255',
            'services_razorpay_webhook_secret' => 'nullable|string|max:255',
            'services_phonepe_merchant_id' => 'required_if:payment_active_gateway,phonepe|nullable|string|max:255',
            'services_phonepe_salt_key' => 'required_if:payment_active_gateway,phonepe|nullable|string|max:255',
            'services_phonepe_salt_index' => 'required_if:payment_active_gateway,phonepe|nullable|string|max:255',
            'services_phonepe_env' => 'required_if:payment_active_gateway,phonepe|nullable|string|in:sandbox,production',
        ]);

        $settings = [
            'payment.active_gateway' => ['value' => $validated['payment_active_gateway'], 'group' => 'payment', 'description' => 'Active payment gateway provider'],
        ];

        if ($request->has('services_razorpay_key')) {
            $settings['payment.razorpay.key'] = ['value' => $validated['services_razorpay_key'] ?? '', 'group' => 'payment', 'encrypted' => true, 'description' => 'Razorpay API Key ID'];
        }
        if ($request->has('services_razorpay_secret')) {
            $settings['payment.razorpay.secret'] = ['value' => $validated['services_razorpay_secret'] ?? '', 'group' => 'payment', 'encrypted' => true, 'description' => 'Razorpay API Secret'];
        }
        if ($request->has('services_razorpay_webhook_secret')) {
            $settings['payment.razorpay.webhook_secret'] = ['value' => $validated['services_razorpay_webhook_secret'] ?? '', 'group' => 'payment', 'encrypted' => true, 'description' => 'Razorpay Webhook Secret'];
        }

        if ($request->has('services_phonepe_merchant_id')) {
            $settings['payment.phonepe.key'] = ['value' => $validated['services_phonepe_merchant_id'] ?? '', 'group' => 'payment', 'encrypted' => true, 'description' => 'PhonePe Merchant ID'];
        }
        if ($request->has('services_phonepe_salt_key')) {
            $settings['payment.phonepe.secret'] = ['value' => $validated['services_phonepe_salt_key'] ?? '', 'group' => 'payment', 'encrypted' => true, 'description' => 'PhonePe Salt Key'];
        }
        if ($request->has('services_phonepe_salt_index')) {
            $settings['payment.phonepe.webhook_secret'] = ['value' => $validated['services_phonepe_salt_index'] ?? '1', 'group' => 'payment', 'encrypted' => true, 'description' => 'PhonePe Salt Index'];
        }
        if ($request->has('services_phonepe_env')) {
            $settings['payment.phonepe.env'] = ['value' => $validated['services_phonepe_env'] ?? 'sandbox', 'group' => 'payment', 'description' => 'PhonePe Environment'];
        }

        $this->settings->setMultiple($settings);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index', ['tab' => 'payment'])
            ->with('success', 'Payment gateway credentials saved. Configuration cache cleared automatically.');
    }

    public function testConnection(Request $request): JsonResponse
    {
        try {
            $gatewayName = $request->input('gateway', 'razorpay');

            $tempConfig = [
                'key' => $request->input('key', $this->settings->get("payment.{$gatewayName}.key")),
                'secret' => $request->input('secret', $this->settings->get("payment.{$gatewayName}.secret")),
                'webhook_secret' => $request->input('webhook_secret', $this->settings->get("payment.{$gatewayName}.webhook_secret")),
                'env' => $request->input('env', $this->settings->get("payment.{$gatewayName}.env", 'sandbox')),
            ];

            $gateway = app(\App\Services\Payment\GatewayManager::class)->driver($gatewayName);
            $gateway->configure($tempConfig);

            $result = $gateway->testConnection();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ {$gatewayName} connection verified successfully. API responded correctly.",
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "❌ {$gatewayName} connection failed. Please check your API credentials.",
            ], 422);
        } catch (PaymentGatewayException $e) {
            return response()->json([
                'success' => false,
                'message' => "❌ Connection error: {$e->getMessage()}",
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Test connection failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "❌ Unexpected error: {$e->getMessage()}",
            ], 500);
        }
    }

    public function updateSubscription(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_default_duration' => 'integer|min:1|max:36500',
            'subscription_trial_days' => 'integer|min:0|max:365',
            'subscription_cancellation_mode' => 'string|in:end_of_period,immediate',
            'subscription_auto_renew' => 'nullable|string',
            'subscription_invoice_footer' => 'nullable|string|max:500',
            'subscription_grace_period' => 'integer|min:0|max:365',
        ]);

        $this->settings->setMultiple([
            'subscription.default_duration' => ['value' => (int) ($validated['subscription_default_duration'] ?? 30), 'group' => 'subscription', 'description' => 'Default subscription duration in days'],
            'subscription.trial_days' => ['value' => (int) ($validated['subscription_trial_days'] ?? 0), 'group' => 'subscription', 'description' => 'Default trial period in days'],
            'subscription.cancel_mode' => ['value' => $validated['subscription_cancellation_mode'] ?? 'end_of_period', 'group' => 'subscription', 'description' => 'Default cancellation mode'],
            'subscription.auto_renew' => ['value' => $request->boolean('subscription_auto_renew', true), 'type' => 'boolean', 'group' => 'subscription', 'description' => 'Enable auto-renewal by default'],
            'subscription.invoice_footer' => ['value' => $validated['subscription_invoice_footer'] ?? '', 'group' => 'subscription', 'description' => 'Invoice footer text'],
            'subscription.grace_period' => ['value' => (int) ($validated['subscription_grace_period'] ?? 7), 'group' => 'subscription', 'description' => 'Grace period in days after expiry'],
        ]);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index', ['tab' => 'subscription'])
            ->with('success', 'Subscription settings saved successfully.');
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'admin_email' => 'nullable|email|max:255',
            'app_currency' => 'required|string|size:3',
            'app_currency_symbol' => 'required|string|max:10',
            'profile_approval_required' => 'nullable|string',
            'default_profile_type' => 'required|string|in:personal,seller,creator',
        ]);

        $this->settings->setMultiple([
            'general.site_name' => ['value' => $validated['app_name'], 'group' => 'general', 'description' => 'Site name'],
            'general.admin_email' => ['value' => $validated['admin_email'] ?? '', 'group' => 'general', 'description' => 'Admin email address'],
            'general.site_currency' => ['value' => $validated['app_currency'], 'group' => 'general', 'description' => 'Default currency'],
            'general.site_currency_symbol' => ['value' => $validated['app_currency_symbol'], 'group' => 'general', 'description' => 'Currency symbol'],
            'general.profile_approval_required' => ['value' => $request->boolean('profile_approval_required', true), 'type' => 'boolean', 'group' => 'general', 'description' => 'Require admin approval for profile switches'],
            'general.default_profile_type' => ['value' => $validated['default_profile_type'], 'group' => 'general', 'description' => 'Default profile type for new users'],
        ]);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index', ['tab' => 'general'])
            ->with('success', 'General settings saved successfully.');
    }
}
