<?php

namespace App\Jobs;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserShipping;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;

class ProcessOrderPayment implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public string $paymentId;
    public string $gatewayOrderId;
    public string $gatewayChecksum;
    public string $gateway;

    public $timeout = 120;
    public $tries = 3;
    public $backoff = [2, 10, 30];
    public $uniqueFor = 3600;

    public function uniqueId(): string
    {
        return (string) $this->orderId;
    }

    public function __construct(
        int $orderId,
        string $paymentId,
        string $gatewayOrderId = '',
        string $gatewayChecksum = '',
        string $gateway = 'razorpay',
    ) {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->gatewayOrderId = $gatewayOrderId;
        $this->gatewayChecksum = $gatewayChecksum;
        $this->gateway = $gateway;
    }

    public function __set(string $name, mixed $value): void
    {
        if ($name === 'razorpayOrderId') {
            $this->gatewayOrderId = $value;
        } elseif ($name === 'razorpaySignature') {
            $this->gatewayChecksum = $value;
        }
    }

    public function middleware()
    {
        return [new WithoutOverlapping((string) $this->orderId)];
    }

    public function handle()
    {
        Log::info('ProcessOrderPayment job started', [
            'order_id' => $this->orderId,
            'gateway' => $this->gateway,
        ]);

        try {
            DB::transaction(function () {
                $order = UserOrder::lockForUpdate()->find($this->orderId);

                if (!$order) {
                    Log::error('Order not found', ['order_id' => $this->orderId]);
                    return;
                }

                if ($order->payment_status === (UserOrder::PAYMENT_PAID ?? 'paid')) {
                    Log::warning('Order already marked as paid. Skipping.', ['order_id' => $order->id]);
                    return;
                }

                $payment = UserPayment::where('order_id', $order->id)
                    ->where('status', 'initiated')
                    ->latest()
                    ->first();

                if (!$payment) {
                    Log::error('Payment record not found or already processed', ['order_id' => $order->id]);
                } else {
                    $currentMeta = is_array($payment->meta) ? $payment->meta : (is_string($payment->meta) ? json_decode($payment->meta, true) : []);

                    $currentMeta['gateway_order_id'] = $this->gatewayOrderId;
                    $currentMeta['gateway_checksum'] = $this->gatewayChecksum;

                    $payment->update([
                        'status' => UserPayment::STATUS_PAID,
                        'transaction_id' => $this->paymentId,
                        'meta' => $currentMeta,
                    ]);

                    Log::info('Payment record updated successfully', [
                        'payment_id' => $payment->id,
                        'gateway' => $this->gateway,
                    ]);
                }

                $order->update([
                    'payment_status' => UserOrder::PAYMENT_PAID ?? 'paid',
                    'status' => UserOrder::STATUS_PROCESSING ?? 'processing',
                ]);

                UserOrder::where('parent_id', $order->id)->update([
                    'payment_status' => UserOrder::PAYMENT_PAID ?? 'paid',
                    'status' => UserOrder::STATUS_PROCESSING ?? 'processing',
                ]);

                Log::info('Order payment status updated', ['order_id' => $order->id]);

                $childOrders = UserOrder::where('parent_id', $order->id)->get();
                $address = $order->address;

                if ($address) {
                    foreach ($childOrders as $childOrder) {
                        if (!$childOrder->shipping) {
                            UserShipping::create([
                                'uuid' => (string) Str::uuid(),
                                'order_id' => $childOrder->id,
                                'address_id' => $address->id,
                                'status' => 'pending',
                            ]);

                            Log::info('Shipping record created for Child Order', [
                                'child_order_id' => $childOrder->id,
                            ]);
                        }
                    }
                } else {
                    Log::warning('No address found to create shipping', ['order_id' => $order->id]);
                }

                Log::info('ProcessOrderPayment job completed', ['order_id' => $order->id]);

                try {
                    $buyer = User::find($order->buyer_id);
                    if ($buyer) {
                        $notificationService = app(NotificationService::class);
                        $notificationService->sendToUser($buyer, 'payment_success', [
                            'title'      => 'Payment Successful',
                            'message'    => 'Your payment of ₹' . number_format($order->total_amount, 2) . ' for order #' . $order->id . ' was successful.',
                            'order_id'   => $order->id,
                            'order_uuid' => $order->uuid,
                            'amount'     => $order->total_amount,
                            'action_url' => null,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('Payment notification failed', ['error' => $e->getMessage()]);
                }
            });
        } catch (Throwable $e) {
            Log::error('Exception in ProcessOrderPayment job', [
                'order_id' => $this->orderId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(?\Throwable $exception = null): void
    {
        Log::error('ProcessOrderPayment permanently failed', [
            'order_id' => $this->orderId,
            'gateway' => $this->gateway,
            'error' => $exception?->getMessage(),
        ]);
    }
}
