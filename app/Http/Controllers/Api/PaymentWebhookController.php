<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Models\UserSubscription;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Payment\PaymentOrchestrator;
use App\Services\Setting\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected ?PaymentGatewayInterface $gateway = null;

    public function __construct(
        protected GatewayManager $gatewayManager,
        protected PaymentOrchestrator $orchestrator,
        protected SettingService $settings,
    ) {}

    public function handle(string $gateway, Request $request): JsonResponse
    {
        try {
            $this->gateway = $this->gatewayManager->driver($gateway);

            if ($gateway === 'razorpay') {
                return $this->handleRazorpay($request);
            }

            if ($gateway === 'phonepe') {
                return $this->handlePhonePe($request);
            }

            return response()->json(['status' => 'error', 'message' => 'Unsupported gateway'], 400);
        } catch (PaymentGatewayException $e) {
            Log::error("{$gateway} webhook: gateway error", ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 502);
        } catch (\Throwable $e) {
            Log::error("{$gateway} webhook: processing failed", ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Processing failed'], 500);
        }
    }

    protected function handleRazorpay(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature');

        if (!$signature) {
            Log::warning('Razorpay webhook: missing signature');
            return response()->json(['status' => 'error', 'message' => 'Missing signature'], 400);
        }

        $webhookSecret = config('services.razorpay.webhook_secret');

        if (empty($webhookSecret)) {
            Log::warning('Razorpay webhook: webhook secret not configured');
            return response()->json(['status' => 'error', 'message' => 'Webhook not configured'], 500);
        }

        $payload = $request->getContent();

        if (!$this->gateway->verifyWebhookSignature($payload, $signature, $webhookSecret)) {
            Log::warning('Razorpay webhook: invalid signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $payloadData = $request->input('payload', []);
        $eventId = $payloadData['payment']['entity']['id']
            ?? $payloadData['subscription']['entity']['id']
            ?? $request->input('event_id');

        $dedupKey = $eventId ? "webhook_dedup_razorpay_{$eventId}" : null;

        if ($dedupKey && Cache::get($dedupKey)) {
            Log::info('Razorpay webhook: duplicate event skipped', ['event_id' => $eventId]);
            return response()->json(['status' => 'duplicate']);
        }

        $event = $this->gateway->parseWebhookEvent($request->all());
        $subscriptionEntity = $payloadData['subscription']['entity'] ?? null;
        $paymentEntity = $payloadData['payment']['entity'] ?? null;

        $gatewaySubId = $subscriptionEntity['id'] ?? $paymentEntity['subscription_id'] ?? null;

        if ($gatewaySubId) {
            $subscription = UserSubscription::where('gateway_subscription_id', $gatewaySubId)->first();

            if (!$subscription) {
                Log::warning('Razorpay webhook: subscription not found', ['gateway_id' => $gatewaySubId]);
                return response()->json(['status' => 'error', 'message' => 'Subscription not found'], 404);
            }

            match ($event) {
                'subscription.charged' => $this->handleCharged($subscription, $paymentEntity),
                'subscription.cancelled' => $this->handleCancelled($subscription),
                'subscription.completed' => $this->handleCompleted($subscription),
                'payment.failed' => $this->handlePaymentFailed($subscription),
                default => Log::info('Razorpay webhook: unhandled subscription event', ['event' => $event]),
            };
        } else {
            if (str_starts_with($event, 'subscription.')) {
                Log::warning('Razorpay webhook: subscription event missing subscription ID', ['event' => $event]);
                return response()->json(['status' => 'error', 'message' => 'No subscription ID'], 400);
            }
            Log::info('Razorpay webhook: direct payment event received', ['event' => $event]);
        }

        if ($dedupKey) {
            Cache::put($dedupKey, true, 3600);
        }

        $this->orchestrator->handleWebhookEvent('razorpay', $event, $request->all());

        return response()->json(['status' => 'success']);
    }

    protected function handlePhonePe(Request $request): JsonResponse
    {
        $signature = $request->header('X-VERIFY');
        $base64Response = $request->input('response');

        if (!$signature || !$base64Response) {
            Log::warning('PhonePe webhook: missing signature or response data');
            return response()->json(['status' => 'error', 'message' => 'Missing signature or data'], 400);
        }

        $gatewayConfig = $this->settings->getGatewayConfig('phonepe');
        $webhookSecret = $gatewayConfig['secret'] ?? '';

        if (empty($webhookSecret)) {
            Log::warning('PhonePe webhook: Salt Key not configured');
            return response()->json(['status' => 'error', 'message' => 'Webhook not configured'], 500);
        }

        if (!$this->gateway->verifyWebhookSignature($base64Response, $signature, $webhookSecret)) {
            Log::warning('PhonePe webhook: invalid signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $decodedPayload = json_decode(base64_decode($base64Response), true);
        Log::info('PhonePe webhook payload decoded', ['payload' => $decodedPayload]);

        $event = $this->gateway->parseWebhookEvent($decodedPayload);

        // Check if this is a subscription webhook
        $merchantSubscriptionId = $decodedPayload['paymentFlow']['merchantSubscriptionId']
            ?? $decodedPayload['data']['paymentFlow']['merchantSubscriptionId']
            ?? null;

        if ($merchantSubscriptionId) {
            $subscription = UserSubscription::where('gateway_subscription_id', $merchantSubscriptionId)->first();

            if (!$subscription) {
                Log::warning('PhonePe webhook: subscription not found', ['gateway_id' => $merchantSubscriptionId]);
                return response()->json(['status' => 'error', 'message' => 'Subscription not found'], 404);
            }

            if ($event === 'subscription.charged') {
                $paymentDetails = $decodedPayload['paymentDetails'][0]
                    ?? $decodedPayload['data']['paymentDetails'][0]
                    ?? null;

                $paymentEntity = [
                    'id' => $paymentDetails['transactionId'] 
                        ?? $decodedPayload['data']['transactionId'] 
                        ?? $decodedPayload['orderId'] 
                        ?? $merchantSubscriptionId,
                    'amount' => $paymentDetails['amount'] 
                        ?? $decodedPayload['data']['amount'] 
                        ?? $decodedPayload['amount'] 
                        ?? 0,
                    'invoice_id' => $decodedPayload['orderId'] 
                        ?? $decodedPayload['data']['orderId'] 
                        ?? null,
                ];

                $this->handleCharged($subscription, $paymentEntity);
                Log::info('PhonePe webhook: subscription charged successfully', ['subscription_id' => $subscription->id]);
            } else {
                $this->handlePaymentFailed($subscription);
                Log::warning('PhonePe webhook: subscription payment failed', ['subscription_id' => $subscription->id]);
            }

            $this->orchestrator->handleWebhookEvent('phonepe', $event, $decodedPayload);
            return response()->json(['status' => 'success']);
        }

        $merchantTransactionId = $decodedPayload['data']['merchantTransactionId'] ?? null;

        if (!$merchantTransactionId) {
            Log::warning('PhonePe webhook: no merchantTransactionId');
            return response()->json(['status' => 'error', 'message' => 'Missing transaction ID'], 400);
        }

        $dedupKey = "webhook_dedup_phonepe_{$merchantTransactionId}";
        if (Cache::get($dedupKey)) {
            Log::info('PhonePe webhook: duplicate event skipped', ['merchantTransactionId' => $merchantTransactionId]);
            return response()->json(['status' => 'duplicate']);
        }

        $order = UserOrder::where('uuid', $merchantTransactionId)->first();

        if ($event === 'payment.success') {
            if (!$order) {
                Log::warning('PhonePe webhook: order not found', ['merchantTransactionId' => $merchantTransactionId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            $transactionId = $decodedPayload['data']['transactionId'] ?? $merchantTransactionId;

            \App\Jobs\ProcessOrderPayment::dispatch(
                $order->id,
                $transactionId,
                $merchantTransactionId,
                $signature,
                'phonepe',
            );

            Cache::put($dedupKey, true, 3600);

            Log::info('PhonePe webhook: payment.success processed', ['order_id' => $order->id]);
        } else {
            Log::warning('PhonePe webhook: payment failed or unhandled', [
                'code' => $decodedPayload['code'] ?? 'unknown',
                'order_id' => $order?->id,
            ]);

            if ($order) {
                $payment = UserPayment::where('order_id', $order->id)
                    ->where('status', 'initiated')
                    ->first();

                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'meta' => array_merge(is_array($payment->meta) ? $payment->meta : [], [
                            'webhook_failure_response' => $decodedPayload,
                        ]),
                    ]);
                }
            }
        }

        $this->orchestrator->handleWebhookEvent('phonepe', $event, $decodedPayload);

        return response()->json(['status' => 'success']);
    }

    protected function handleCharged(UserSubscription $subscription, ?array $paymentEntity): void
    {
        DB::transaction(function () use ($subscription, $paymentEntity) {
            $locked = UserSubscription::where('id', $subscription->id)
                ->lockForUpdate()
                ->firstOrFail();

            $chargedAmount = $paymentEntity['amount'] ?? 0;
            $expectedAmount = $locked->plan ? (int) round($locked->plan->price * 100) : 0;

            if ($expectedAmount > 0 && $chargedAmount !== $expectedAmount) {
                Log::critical('Webhook: amount mismatch', [
                    'subscription_id' => $locked->id,
                    'charged' => $chargedAmount,
                    'expected' => $expectedAmount,
                ]);
            }

            $gatewayInvoiceId = $paymentEntity['invoice_id'] ?? null;

            if ($gatewayInvoiceId) {
                $existingInvoice = \App\Models\Invoice::where('gateway_invoice_id', $gatewayInvoiceId)
                    ->lockForUpdate()
                    ->first();

                if ($existingInvoice) {
                    Log::info('Webhook: duplicate charge skipped', [
                        'subscription_id' => $locked->id,
                        'gateway_invoice_id' => $gatewayInvoiceId,
                    ]);
                    return;
                }
            }

            $locked->update([
                'status' => 'active',
                'gateway_payment_id' => $paymentEntity['id'] ?? $locked->gateway_payment_id,
                'next_billing_at' => $locked->plan->duration_days
                    ? now()->addDays($locked->plan->duration_days)
                    : null,
                'gateway_response' => array_merge(
                    $locked->gateway_response ?? [],
                    ['charged_at' => now()->toIso8601String()]
                ),
            ]);

            $invoice = $locked->generateInvoice();
            $invoice->markAsPaid(
                gatewayInvoiceId: $gatewayInvoiceId,
                paymentMethod: 'razorpay',
            );

            Log::info('Subscription charged + invoice generated', [
                'subscription_id' => $locked->id,
                'invoice_id' => $invoice->id,
            ]);
        });
    }

    protected function handleCancelled(UserSubscription $subscription): void
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
            'next_billing_at' => null,
        ]);
    }

    protected function handleCompleted(UserSubscription $subscription): void
    {
        $subscription->update(['status' => 'expired']);
    }

    protected function handlePaymentFailed(UserSubscription $subscription): void
    {
        $subscription->update(['status' => 'past_due']);
    }
}
