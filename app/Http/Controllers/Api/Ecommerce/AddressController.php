<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Ecommerce\StoreAddressRequest;
use App\Models\Ecommerce\UserAddress;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    /**
     * List user's saved addresses
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = UserAddress::where('user_id', $request->user()->id)
            ->where('type', 'account')  // Or separate 'shipping' vs 'account'
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /**
     * Save a new address (vouchers etc)
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = UserAddress::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id,
            'type' => 'account'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
            'data' => $address
        ]);
    }
}
