<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\CheckoutRequest;
use App\Services\Ecommerce\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        try {
            $result = $this->checkoutService->checkout($data, $user);

            return response()->json([
                'success' => true,
                'order' => $result['order'],
                'price_updates' => $result['price_updates'],
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
