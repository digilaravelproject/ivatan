<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserOrderItem;
use Carbon\Carbon;

class RestoreAbandonedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:restore-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel pending orders older than 30 minutes and restore inventory stock.';

    public function handle()
    {
        $this->info("Scanning for abandoned orders (older than 30 minutes)...");

        $abandonedOrders = UserOrder::where('status', UserOrder::STATUS_PENDING)
            ->where('payment_status', UserOrder::PAYMENT_PENDING)
            ->where('created_at', '<', Carbon::now()->subMinutes(30))
            ->get();

        if ($abandonedOrders->isEmpty()) {
            $this->info("No abandoned orders found.");
            return;
        }

        $restoredCount = 0;

        foreach ($abandonedOrders as $order) {
            try {
                DB::transaction(function () use ($order) {
                    // Lock the order for update to prevent race conditions during the cron job
                    $lockedOrder = UserOrder::lockForUpdate()->find($order->id);

                    if (!$lockedOrder || $lockedOrder->status !== UserOrder::STATUS_PENDING) {
                        return; // Order was processed or changed since we fetched it
                    }

                    // 1. Mark the Parent Order as failed/cancelled
                    $lockedOrder->update([
                        'status' => 'cancelled',
                        'payment_status' => 'failed',
                    ]);

                    // 2. Mark Child Orders as failed/cancelled
                    if ($lockedOrder->parent_id === null) {
                        $childOrders = UserOrder::lockForUpdate()->where('parent_id', $lockedOrder->id)->get();
                        foreach ($childOrders as $child) {
                            $child->update([
                                'status' => 'cancelled',
                                'payment_status' => 'failed',
                            ]);
                        }
                    }

                    // 3. Restore Stock
                    $items = UserOrderItem::where('order_id', $lockedOrder->id)->get();
                    foreach ($items as $item) {
                        if ($item->item_type === 'user_products') {
                            $product = UserProduct::lockForUpdate()->find($item->item_id);
                            if ($product) {
                                $product->increment('stock', $item->quantity);
                                \Log::info("Restored Stock: Cron job automatically restored {$item->quantity} for Product {$product->id} due to abandoned Order {$lockedOrder->id}.");
                            }
                        }
                    }
                });

                $restoredCount++;
                $this->line("Restored abandoned Order ID: {$order->id}");

            } catch (\Throwable $e) {
                \Log::error("Failed to restore abandoned Order ID: {$order->id}", [
                    'error' => $e->getMessage()
                ]);
                $this->error("Failed to restore Order ID: {$order->id}");
            }
        }

        $this->info("Process complete. Successfully restored {$restoredCount} abandoned order(s).");
    }
}
