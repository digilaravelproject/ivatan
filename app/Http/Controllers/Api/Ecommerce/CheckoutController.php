<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CheckoutRequest;
use App\Models\Ecommerce\UserOrder;
use App\Models\User;
use App\Services\Ecommerce\CheckoutService;
use App\Services\NotificationService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected NotificationService $notificationService;

    public function __construct(CheckoutService $checkoutService, NotificationService $notificationService)
    {
        $this->checkoutService = $checkoutService;
        $this->notificationService = $notificationService;
    }

    public function checkout(CheckoutRequest $request)
    {
        /** @var \Illuminate\Http\Request $request */
        $data = $request->validated();
        $user = $request->user();

        $lock = Cache::lock('checkout_lock_user_' . $user->id, 10);

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'error' => 'Checkout is already in progress. Please wait.'
            ], 429);
        }

        try {
            $result = $this->checkoutService->checkout($data, $user);

            // Notify sellers of new order (non-blocking)
            try {
                $parentOrder = $result['order'];
                $childOrders = UserOrder::where('parent_id', $parentOrder->id)
                    ->whereNotNull('seller_id')
                    ->with('seller')
                    ->get();

                foreach ($childOrders as $childOrder) {
                    if ($childOrder->seller && $childOrder->seller_id !== $user->id) {
                        $this->notificationService->sendToUser($childOrder->seller, 'new_order', [
                            'title'       => 'New Order',
                            'message'     => 'You received a new order from ' . $user->name,
                            'order_id'    => $childOrder->id,
                            'order_uuid'  => $childOrder->uuid,
                            'amount'      => $childOrder->total_amount,
                            'buyer_name'  => $user->name,
                            'buyer_id'    => $user->id,
                            'action_url'  => null,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Order notification failed', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'order' => $result['order'],
                'price_updates' => $result['price_updates'],
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['success' => false, 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        } finally {
            $lock->release();
        }
    }
}
