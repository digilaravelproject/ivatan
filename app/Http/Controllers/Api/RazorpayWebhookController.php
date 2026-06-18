<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\UserSubscription;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    protected ?PaymentGatewayInterface $gateway = null;

    public function __construct(
        protected GatewayManager $gatewayManager
    ) {}

    protected function gateway(): PaymentGatewayInterface
    {
        return $this->gateway ??= $this->gatewayManager->driver('razorpay');
    }

    public function handle(Request $request): JsonResponse
    {
        try {
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

            if (!$this->gateway()->verifyWebhookSignature($payload, $signature, $webhookSecret)) {
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

            $event = $this->gateway()->parseWebhookEvent($request->all());
            $subscriptionEntity = $payloadData['subscription']['entity'] ?? null;
            $paymentEntity = $payloadData['payment']['entity'] ?? null;

            $gatewaySubId = $subscriptionEntity['id'] ?? $paymentEntity['subscription_id'] ?? null;

            if (!$gatewaySubId) {
                if (str_starts_with($event, 'subscription.')) {
                    Log::warning('Razorpay webhook: no subscription ID', ['event' => $event]);
                    return response()->json(['status' => 'error', 'message' => 'No subscription ID'], 400);
                }
                Log::info('Razorpay webhook: direct payment event ignored on subscription webhook', ['event' => $event]);
                return response()->json(['status' => 'success', 'message' => 'Ignored non-subscription event']);
            }

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
                default => Log::info('Razorpay webhook: unhandled event', ['event' => $event]),
            };

            if ($dedupKey) {
                Cache::put($dedupKey, true, 3600);
            }

            return response()->json(['status' => 'success']);
        } catch (PaymentGatewayException $e) {
            Log::error('Razorpay webhook: gateway error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 502);
        } catch (\Throwable $e) {
            Log::error('Razorpay webhook: processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Processing failed'], 500);
        }
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
                Log::critical('Razorpay webhook: amount mismatch', [
                    'subscription_id' => $locked->id,
                    'charged' => $chargedAmount,
                    'expected' => $expectedAmount,
                ]);
            }

            $gatewayInvoiceId = $paymentEntity['invoice_id'] ?? null;

            if ($gatewayInvoiceId) {
                $existingInvoice = Invoice::where('gateway_invoice_id', $gatewayInvoiceId)
                    ->lockForUpdate()
                    ->first();

                if ($existingInvoice) {
                    Log::info('Razorpay webhook: duplicate charge skipped', [
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

            Log::info('Razorpay subscription charged + invoice generated', [
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

        Log::info('Razorpay subscription cancelled via webhook', [
            'subscription_id' => $subscription->id,
        ]);
    }

    protected function handleCompleted(UserSubscription $subscription): void
    {
        $subscription->update(['status' => 'expired']);

        Log::info('Razorpay subscription completed', [
            'subscription_id' => $subscription->id,
        ]);
    }

    protected function handlePaymentFailed(UserSubscription $subscription): void
    {
        $subscription->update(['status' => 'past_due']);

        Log::warning('Razorpay payment failed', [
            'subscription_id' => $subscription->id,
        ]);
    }
}
