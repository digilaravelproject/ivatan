<?php

namespace App\Jobs;

use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserOrderItem;
use App\Models\Ecommerce\UserPayment;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserShipping;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Razorpay\Api\Api;


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
                    // Get the current meta value and decode it to an array
                    $currentMeta = json_decode($payment->meta, true) ?? [];
                    // Add the new keys to the existing meta array
                    $currentMeta['razorpay_order_id'] = $this->razorpayOrderId;
                    $currentMeta['razorpay_signature'] = $this->razorpaySignature;

                    // Update the payment record, keeping the existing meta values and adding new ones
                    $payment->update([
                        'status' => UserPayment::STATUS_SUCCESSFUL ?? 'successful',
                        'transaction_id' => $this->paymentId,
                        'meta' => json_encode($currentMeta),  // Re-encode the updated meta array
                    ]);


                    Log::info('Payment record updated successfully', ['payment_id' => $payment->id]);
                }

                // Update order status
                $order->update([
                    'payment_status' => UserOrder::PAYMENT_PAID ?? 'paid',
                    'status' => UserOrder::STATUS_PROCESSING ?? 'processing',
                ]);

                Log::info('Order payment status updated', ['order_id' => $order->id]);

                // Update stock for each item
                foreach ($order->items as $item) {
                    if ($item->item_type === 'user_products') {
                        $product = UserProduct::lockForUpdate()->find($item->item_id);
                        if ($product) {
                            $oldStock = $product->stock;
                            $product->stock = max(0, $product->stock - $item->quantity);
                            $product->save();

                            Log::info('Stock updated', [
                                'product_id' => $product->id,
                                'old_stock' => $oldStock,
                                'new_stock' => $product->stock,
                                'quantity_sold' => $item->quantity,
                            ]);
                        } else {
                            Log::warning('Product not found for stock update', [
                                'product_id' => $item->item_id,
                            ]);
                        }
                    }
                }

                // Create shipping
                if (!$order->shipping) {
                    $address = $order->address;

                    if ($address) {
                        $shipping = UserShipping::create([
                            'uuid' => (string) Str::uuid(),
                            'order_id' => $order->id,
                            'address_id' => $address->id,
                            'status' => 'pending',
                        ]);

                        Log::info('Shipping record created', [
                            'shipping_id' => $shipping->id,
                            'order_id' => $order->id,
                        ]);
                    } else {
                        Log::warning('No address found to create shipping', ['order_id' => $order->id]);
                    }
                } else {
                    Log::info('Shipping already exists for order', ['order_id' => $order->id]);
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
