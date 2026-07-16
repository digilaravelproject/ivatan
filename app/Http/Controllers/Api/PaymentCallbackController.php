<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdPayment;
use App\Models\Ecommerce\UserOrder;
use App\Models\UserSubscription;
use App\Models\ExclusiveContentEnablement;
use App\Models\ExclusiveContentPurchase;
use App\Services\ExclusiveContentService;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\PaymentOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function __construct(
        protected GatewayManager $gatewayManager,
        protected PaymentOrchestrator $orchestrator,
    ) {}

    public function handle(string $gateway, Request $request): Response
    {
        try {
            Log::info("Payment callback received for {$gateway}", $request->all());

            if ($gateway === 'phonepe') {
                return $this->handlePhonePeCallback($request);
            }

            Log::warning('Payment callback: unsupported gateway', ['gateway' => $gateway]);
            return $this->redirectFailed();
        } catch (\Throwable $e) {
            Log::error('Payment callback failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);
            return $this->redirectFailed();
        }
    }

    protected function handlePhonePeCallback(Request $request): Response
    {
        $code = $request->input('code');
        $merchantTransactionId = $request->input('merchantTransactionId')
            ?? $request->input('transactionId');

        if ($code !== 'PAYMENT_SUCCESS') {
            Log::warning('PhonePe callback: payment not successful', [
                'code' => $code,
                'merchantTransactionId' => $merchantTransactionId,
            ]);
            return $this->redirectFailed();
        }

        if (!$merchantTransactionId) {
            Log::warning('PhonePe callback: missing transaction ID');
            return $this->redirectFailed();
        }

        $order = UserOrder::where('uuid', $merchantTransactionId)->first();
        $adPayment = AdPayment::where('gateway_order_id', $merchantTransactionId)->first();
        $subscription = UserSubscription::where('gateway_order_id', $merchantTransactionId)->first();

        if ($order) {
            try {
                $this->orchestrator->resetDriver();
                $payload = ['merchantTransactionId' => $merchantTransactionId];

                $result = $this->orchestrator->verifyEcommercePayment($order, $payload);

                if ($result->success) {
                    $this->orchestrator->processEcommercePayment($order, $result, $payload);
                    Log::info('PhonePe callback: payment verified & job dispatched', [
                        'order_id' => $order->id,
                    ]);
                    return $this->redirectSuccess(['order_id' => $order->id]);
                }

                Log::warning('PhonePe callback: verify failed for order', [
                    'order_id' => $order->id,
                    'message' => $result->message,
                ]);
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: exception for order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($adPayment) {
            try {
                $this->orchestrator->resetDriver();
                $payload = ['merchantTransactionId' => $merchantTransactionId];

                $result = $this->orchestrator->verifyAdPayment($adPayment, $payload);

                if ($result->success) {
                    $this->orchestrator->processAdPayment($adPayment, $adPayment->ad, $result);
                    Log::info('PhonePe callback: ad payment verified', [
                        'ad_payment_id' => $adPayment->id,
                    ]);
                    return $this->redirectSuccess(['ad_id' => $adPayment->ad_id]);
                }
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: exception for ad', [
                    'ad_payment_id' => $adPayment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($subscription) {
            try {
                $this->orchestrator->resetDriver();
                $result = $this->orchestrator->driver()->verifyPayment(['merchantTransactionId' => $merchantTransactionId]);

                if ($result->success) {
                    DB::transaction(function () use ($subscription, $result, $merchantTransactionId) {
                        $lockedSub = UserSubscription::where('id', $subscription->id)->lockForUpdate()->firstOrFail();
                        
                        $lockedSub->update([
                            'status' => 'active',
                            'gateway_payment_id' => $result->gatewayPaymentId ?? $merchantTransactionId,
                            'next_billing_at' => $lockedSub->plan->duration_days ? now()->addDays($lockedSub->plan->duration_days) : null,
                            'ends_at' => $lockedSub->plan->duration_days ? now()->addDays($lockedSub->plan->duration_days) : null,
                        ]);

                        $invoice = $lockedSub->generateInvoice();
                        $invoice->markAsPaid(
                            gatewayInvoiceId: $result->gatewayPaymentId ?? $merchantTransactionId,
                            paymentMethod: 'phonepe'
                        );
                    });

                    Log::info('PhonePe callback: subscription verified & processed', [
                        'subscription_id' => $subscription->id,
                    ]);
                    return $this->redirectSuccess(['subscription_id' => $subscription->id]);
                }
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: exception for subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif (str_starts_with($merchantTransactionId, 'ene_')) {
            try {
                $parts = explode('_', $merchantTransactionId);
                $enablementId = $parts[1] ?? null;
                $enablement = ExclusiveContentEnablement::find($enablementId);

                if ($enablement) {
                    $this->orchestrator->resetDriver();
                    $result = $this->orchestrator->driver()->verifyPayment(['merchantTransactionId' => $merchantTransactionId]);

                    if ($result->success) {
                        DB::transaction(function () use ($enablement, $merchantTransactionId) {
                            $enablement->update([
                                'payment_status' => 'completed',
                                'gateway_transaction_id' => $merchantTransactionId,
                                'status' => 'pending',
                            ]);
                        });
                        Log::info('PhonePe callback: creator enablement payment success processed', ['enablement_id' => $enablementId]);
                        return $this->redirectSuccess(['enablement_id' => $enablementId]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: exception for enablement', [
                    'merchantTransactionId' => $merchantTransactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif (str_starts_with($merchantTransactionId, 'exc_')) {
            try {
                $purchaseUuid = substr($merchantTransactionId, 4);
                $purchase = ExclusiveContentPurchase::where('uuid', $purchaseUuid)->first();

                if ($purchase) {
                    $this->orchestrator->resetDriver();
                    $result = $this->orchestrator->driver()->verifyPayment(['merchantTransactionId' => $merchantTransactionId]);

                    if ($result->success) {
                        $exclusiveContentService = app(ExclusiveContentService::class);
                        $exclusiveContentService->processSuccessfulPurchase($purchase);
                        Log::info('PhonePe callback: exclusive content purchase success processed', ['purchase_id' => $purchase->id]);
                        return $this->redirectSuccess(['purchase_id' => $purchase->id]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: exception for purchase', [
                    'merchantTransactionId' => $merchantTransactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('PhonePe callback: no matching order or ad found in database. Attempting direct gateway verification.', [
                'merchantTransactionId' => $merchantTransactionId,
            ]);

            try {
                $this->orchestrator->resetDriver();
                $result = $this->orchestrator->driver()->verifyPayment(['merchantTransactionId' => $merchantTransactionId]);

                if ($result->success) {
                    Log::info('PhonePe callback: payment verified directly with gateway (test/mock flow)', [
                        'merchantTransactionId' => $merchantTransactionId,
                    ]);
                    return $this->redirectSuccess(['test' => true]);
                }
            } catch (\Throwable $e) {
                Log::error('PhonePe callback: gateway verification exception', [
                    'merchantTransactionId' => $merchantTransactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->redirectFailed();
    }

    protected function redirectSuccess(array $params = []): Response
    {
        return response('Payment Successful', 200);
    }

    protected function redirectFailed(): Response
    {
        return response('Payment Failed', 200);
    }
}
