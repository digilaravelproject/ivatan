<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CheckoutRequest;
use App\Services\Ecommerce\CheckoutService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function checkout(CheckoutRequest $request)
    {
        /** @var \Illuminate\Http\Request $request */
        $data = $request->validated();
        // $data = $request->all();
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
