<?php

namespace App\Jobs;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserShipping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;

class ProcessOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public string $paymentId;
    public string $razorpayOrderId;
    public string $razorpaySignature;

    /** ✅ Job control properties */
    public $timeout = 120; // Job fails if it runs more than 2 minutes
    public $tries = 3;     // Retry up to 3 times before failing permanently

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId, string $paymentId, string $razorpayOrderId, string $razorpaySignature)
    {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->razorpayOrderId = $razorpayOrderId;
        $this->razorpaySignature = $razorpaySignature;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new WithoutOverlapping((string) $this->orderId)];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('ProcessOrderPayment job started', [
            'order_id' => $this->orderId,
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

                // Update payment record
                $payment = UserPayment::where('order_id', $order->id)
                    ->where('gateway', 'razorpay')
                    ->where('status', 'initiated')
                    ->first();

                if (!$payment) {
                    Log::error('Payment record not found or already processed', ['order_id' => $order->id]);
                } else {
                    // UserPayment 'meta' is cast to array in the model
                    $currentMeta = is_array($payment->meta) ? $payment->meta : (is_string($payment->meta) ? json_decode($payment->meta, true) : []);

                    // Add the new keys to the existing meta array
                    $currentMeta['razorpay_order_id'] = $this->razorpayOrderId;
                    $currentMeta['razorpay_signature'] = $this->razorpaySignature;

                    // Update the payment record
                    $payment->update([
                        'status' => UserPayment::STATUS_PAID,
                        'transaction_id' => $this->paymentId,
                        'meta' => $currentMeta,
                    ]);


                    Log::info('Payment record updated successfully', ['payment_id' => $payment->id]);
                }

                // Update order status (Parent)
                $order->update([
                    'payment_status' => UserOrder::PAYMENT_PAID ?? 'paid',
                    'status' => UserOrder::STATUS_PROCESSING ?? 'processing',
                ]);

                // Update order status (Children)
                UserOrder::where('parent_id', $order->id)->update([
                    'payment_status' => UserOrder::PAYMENT_PAID ?? 'paid',
                    'status' => UserOrder::STATUS_PROCESSING ?? 'processing',
                ]);

                Log::info('Order payment status updated', ['order_id' => $order->id]);

                // Stock is already deducted during checkout (CheckoutService)
                // Redundant stock deduction removed here to prevent double/triple deduction bug.

                // Create shipping per child order
                $childOrders = UserOrder::where('parent_id', $order->id)->get();
                $address = $order->address;

                if ($address) {
                    foreach ($childOrders as $childOrder) {
                        if (!$childOrder->shipping) {
                            $shipping = UserShipping::create([
                                'uuid' => (string) Str::uuid(),
                                'order_id' => $childOrder->id,
                                'address_id' => $address->id,
                                'status' => 'pending',
                            ]);

                            Log::info('Shipping record created for Child Order', [
                                'shipping_id' => $shipping->id,
                                'child_order_id' => $childOrder->id,
                            ]);
                        }
                    }
                } else {
                    Log::warning('No address found to create shipping', ['order_id' => $order->id]);
                }

                // You can also log the final success if all goes well
                Log::info('ProcessOrderPayment job completed', ['order_id' => $order->id]);
            });
        } catch (Throwable $e) {
            Log::error('Exception in ProcessOrderPayment job', [
                'order_id' => $this->orderId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // rethrow to let Laravel handle failed_jobs table
        }
    }
}
