<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Services\Payment\GatewayManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';
    protected $description = 'Handle pre-debit notifications and recurring debit execution for active subscriptions';

    public function handle(GatewayManager $gatewayManager): int
    {
        $this->info('Starting subscription auto-renew process...');

        // 1. Process Pre-Debit Notifications (For subscriptions renewing within 24-48 hours)
        $this->processPreDebits($gatewayManager);

        // 2. Process Debit Charges (For subscriptions renewing now)
        $this->processDebits($gatewayManager);

        $this->info('Auto-renew process completed.');
        return Command::SUCCESS;
    }

    protected function processPreDebits(GatewayManager $gatewayManager): void
    {
        $targetDate = now()->addHours(30);

        UserSubscription::where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('next_billing_at')
            ->where('next_billing_at', '<=', $targetDate)
            ->whereNotNull('gateway_subscription_id')
            ->chunkById(50, function ($subscriptions) use ($gatewayManager) {
                foreach ($subscriptions as $subscription) {
                    $meta = $subscription->gateway_response ?? [];
                    
                    if (isset($meta['pre_debit_initiated_at']) && now()->parse($meta['pre_debit_initiated_at'])->diffInHours(now()) < 48) {
                        continue;
                    }

                    try {
                        $gatewayName = $subscription->plan->gateway ?? $gatewayManager->getDefaultGateway();
                        $gateway = $gatewayManager->driver($gatewayName);
                        
                        $preDebitTxnId = 'PRE_' . uniqid() . '_' . $subscription->id;
                        $amount = $subscription->plan->price;
                        $merchantUserId = 'USER_' . $subscription->user_id;

                        $result = $gateway->sendPreDebitNotification(
                            $subscription->gateway_subscription_id,
                            $amount,
                            $merchantUserId,
                            $preDebitTxnId
                        );

                        if ($result->success) {
                            $meta['pre_debit_initiated_at'] = now()->toIso8601String();
                            $meta['pre_debit_txn_id'] = $preDebitTxnId;
                            $meta['pre_debit_response'] = $result->rawResponse;
                            
                            $subscription->update([
                                'gateway_response' => $meta
                            ]);

                            $this->info("✅ Pre-debit notification sent successfully for Subscription ID: {$subscription->id}");
                            Log::info("Autopay: Pre-debit notification sent successfully", ['subscription_id' => $subscription->id, 'txn_id' => $preDebitTxnId]);
                        } else {
                            $this->error("❌ Pre-debit notification failed for Subscription ID: {$subscription->id} - {$result->message}");
                            Log::warning("Autopay: Pre-debit notification failed", ['subscription_id' => $subscription->id, 'message' => $result->message]);
                        }
                    } catch (\Throwable $e) {
                        Log::error("Autopay: Exception during pre-debit", ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
                    }
                }
            });
    }

    protected function processDebits(GatewayManager $gatewayManager): void
    {
        UserSubscription::where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('next_billing_at')
            ->where('next_billing_at', '<=', now())
            ->whereNotNull('gateway_subscription_id')
            ->chunkById(50, function ($subscriptions) use ($gatewayManager) {
                foreach ($subscriptions as $subscription) {
                    try {
                        $gatewayName = $subscription->plan->gateway ?? $gatewayManager->getDefaultGateway();
                        $gateway = $gatewayManager->driver($gatewayName);

                        if ($gatewayName === 'phonepe') {
                            $meta = $subscription->gateway_response ?? [];
                            if (!isset($meta['pre_debit_initiated_at'])) {
                                $this->warn("⚠️ Subscription ID: {$subscription->id} is due for debit but no pre-debit notification was found. Triggering pre-debit first.");
                                Log::warning("Autopay: Debit skipped due to missing pre-debit, triggering pre-debit", ['subscription_id' => $subscription->id]);
                                
                                $preDebitTxnId = 'PRE_' . uniqid() . '_' . $subscription->id;
                                $result = $gateway->sendPreDebitNotification($subscription->gateway_subscription_id, $subscription->plan->price, 'USER_' . $subscription->user_id, $preDebitTxnId);
                                if ($result->success) {
                                    $meta['pre_debit_initiated_at'] = now()->toIso8601String();
                                    $meta['pre_debit_txn_id'] = $preDebitTxnId;
                                    $subscription->update(['gateway_response' => $meta]);
                                }
                                continue;
                            }

                            $hoursSincePreDebit = now()->parse($meta['pre_debit_initiated_at'])->diffInHours(now());
                            if ($hoursSincePreDebit < 24) {
                                $this->warn("⏳ Pre-debit was sent {$hoursSincePreDebit} hours ago. PhonePe requires a full 24-hour delay before executing debit for Subscription ID: {$subscription->id}. Skipping until next run.");
                                continue;
                            }
                        }

                        $debitTxnId = 'DEB_' . uniqid() . '_' . $subscription->id;
                        $amount = $subscription->plan->price;
                        $merchantUserId = 'USER_' . $subscription->user_id;

                        $result = $gateway->chargeSubscription(
                            $subscription->gateway_subscription_id,
                            $amount,
                            $merchantUserId,
                            $debitTxnId
                        );

                        if ($result->success) {
                            DB::transaction(function () use ($subscription, $result, $debitTxnId) {
                                $lockedSub = UserSubscription::where('id', $subscription->id)->lockForUpdate()->firstOrFail();
                                
                                $meta = $lockedSub->gateway_response ?? [];
                                unset($meta['pre_debit_initiated_at'], $meta['pre_debit_txn_id'], $meta['pre_debit_response']);
                                $meta['last_debit_at'] = now()->toIso8601String();
                                $meta['last_debit_txn_id'] = $debitTxnId;
                                
                                $lockedSub->update([
                                    'gateway_payment_id' => $result->gatewayPaymentId ?? $debitTxnId,
                                    'next_billing_at' => $lockedSub->plan->duration_days ? now()->addDays($lockedSub->plan->duration_days) : null,
                                    'ends_at' => $lockedSub->plan->duration_days ? now()->addDays($lockedSub->plan->duration_days) : null,
                                    'gateway_response' => $meta
                                ]);

                                $invoice = $lockedSub->generateInvoice();
                                $invoice->markAsPaid(
                                    gatewayInvoiceId: $result->gatewayPaymentId ?? $debitTxnId,
                                    paymentMethod: $lockedSub->plan->gateway ?? 'phonepe'
                                );
                            });

                            $this->info("✅ Auto-renewal successfully executed for Subscription ID: {$subscription->id}");
                            Log::info("Autopay: Renewal executed successfully", ['subscription_id' => $subscription->id, 'txn_id' => $debitTxnId]);
                        } else {
                            $subscription->update([
                                'status' => 'past_due'
                            ]);

                            $this->error("❌ Auto-renewal debit failed for Subscription ID: {$subscription->id} - {$result->message}");
                            Log::error("Autopay: Renewal debit failed", ['subscription_id' => $subscription->id, 'message' => $result->message]);
                        }
                    } catch (\Throwable $e) {
                        Log::error("Autopay: Exception during recurring debit execution", ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
                    }
                }
            });
    }
}
