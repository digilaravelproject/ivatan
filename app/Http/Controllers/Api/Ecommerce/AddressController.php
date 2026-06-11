<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Ecommerce\StoreAddressRequest;
use App\Services\Ecommerce\AddressService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * List user's saved addresses
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $addresses = $this->addressService->listAddresses($request->user());
            return response()->json([
                'success' => true,
                'data' => $addresses
            ]);
        } catch (Exception $e) {
            Log::error('Address index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch addresses.'
            ], 500);
        }
    }

    /**
     * Save a new address
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        try {
            $address = $this->addressService->storeAddress($request->validated(), $request->user());
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully',
                'data' => $address
            ]);
        } catch (Exception $e) {
            Log::error('Address store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save address.'
            ], 500);
        }
    }
}
