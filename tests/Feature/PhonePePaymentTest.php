<?php

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\User;
use App\Models\Setting;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\PhonePeGateway;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    if (!\Spatie\Permission\Models\Role::where('name', 'admin')->where('guard_name', 'web')->exists()) {
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
});

test('admin can update settings with phonepe gateway', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->post(route('admin.settings.update-payment'), [
            'payment_active_gateway' => 'phonepe',
            'services_phonepe_merchant_id' => 'MERCH123',
            'services_phonepe_salt_key' => 'saltkey123',
            'services_phonepe_salt_index' => '1',
            'services_phonepe_env' => 'sandbox',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $settings = app(SettingService::class);
    expect($settings->get('payment.active_gateway'))->toBe('phonepe')
        ->and($settings->get('payment.phonepe.key'))->toBe('MERCH123')
        ->and($settings->get('payment.phonepe.secret'))->toBe('saltkey123');
});

test('phonepe test connection reports success correctly', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Http::fake([
        'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/*' => Http::response([
            'success' => false,
            'code' => 'PAYMENT_ERROR',
            'message' => 'Payment Failed',
        ], 200),
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('admin.settings.test-connection'), [
            'gateway' => 'phonepe',
            'key' => 'MERCH123',
            'secret' => 'saltkey123',
            'webhook_secret' => '1',
            'env' => 'sandbox',
        ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
});

test('createPaymentIntent generates correct payload and signature for phonepe', function () {
    Http::fake([
        'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay' => Http::response([
            'success' => true,
            'code' => 'SUCCESS',
            'data' => [
                'instrumentResponse' => [
                    'redirectInfo' => [
                        'url' => 'https://phonepe.test/redirect-pay',
                    ]
                ]
            ]
        ], 200),
    ]);

    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $order = UserOrder::create([
        'uuid' => (string) \Illuminate\Support\Str::uuid(),
        'buyer_id' => $user->id,
        'total_amount' => 500,
        'payment_status' => 'initiated',
        'status' => 'pending',
    ]);

    // Save PhonePe settings
    $settings = app(SettingService::class);
    $settings->setMultiple([
        'payment.active_gateway' => 'phonepe',
        'payment.phonepe.key' => 'MERCH123',
        'payment.phonepe.secret' => 'saltkey123',
        'payment.phonepe.webhook_secret' => '1',
        'payment.phonepe.env' => 'sandbox',
    ]);

    // Clear config caches
    config([
        'payment.active_gateway' => 'phonepe',
        'services.phonepe.key' => 'MERCH123',
        'services.phonepe.secret' => 'saltkey123',
        'services.phonepe.webhook_secret' => '1',
        'services.phonepe.env' => 'sandbox',
    ]);

    $response = $this->withToken($token)
        ->postJson('/api/v1/payment/razorpay/order', [
            'order_id' => $order->id,
        ]);

    $response->assertOk();
    $response->assertJsonPath('gateway', 'phonepe');
    $response->assertJsonPath('redirect_url', 'https://phonepe.test/redirect-pay');

    $paymentRecord = UserPayment::where('order_id', $order->id)->first();
    expect($paymentRecord)->not->toBeNull()
        ->and($paymentRecord->gateway)->toBe('phonepe')
        ->and($paymentRecord->status)->toBe('initiated');
});

test('phonepe webhook processes success event and triggers order job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $orderUuid = (string) \Illuminate\Support\Str::uuid();
    $order = UserOrder::create([
        'uuid' => $orderUuid,
        'buyer_id' => $user->id,
        'total_amount' => 500,
        'payment_status' => 'initiated',
        'status' => 'pending',
    ]);

    UserPayment::create([
        'order_id' => $order->id,
        'gateway' => 'phonepe',
        'status' => 'initiated',
        'meta' => [
            'gateway_order_id' => $orderUuid,
        ],
    ]);

    $webhookData = [
        'success' => true,
        'code' => 'PAYMENT_SUCCESS',
        'data' => [
            'merchantId' => 'MERCH123',
            'merchantTransactionId' => $orderUuid,
            'transactionId' => 'TXN_PHONEPE_123',
            'amount' => 50000,
        ]
    ];

    $base64Response = base64_encode(json_encode($webhookData));
    $saltKey = 'saltkey123';
    $signature = hash('sha256', $base64Response . $saltKey) . '###1';

    // Set config secret
    config(['services.phonepe.secret' => $saltKey]);

    $response = $this->postJson('/api/webhooks/phonepe', [
        'response' => $base64Response,
    ], [
        'X-VERIFY' => $signature,
    ]);

    $response->assertOk();
    $response->assertJsonPath('status', 'success');

    Queue::assertPushed(\App\Jobs\ProcessOrderPayment::class, function ($job) use ($order) {
        return $job->orderId === $order->id && $job->paymentId === 'TXN_PHONEPE_123';
    });
});
