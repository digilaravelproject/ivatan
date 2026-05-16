<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreSellerFinancialRequest;
use App\Http\Resources\Seller\SellerFinancialResource;
use App\Models\Ecommerce\UserSellerFinancial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerFinancialController extends Controller
{
    /**
     * Get seller's active financial details
     */
    public function show(Request $request): SellerFinancialResource|JsonResponse
    {
        $financial = UserSellerFinancial::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        if (!$financial) {
            return response()->json(['success' => false, 'message' => 'No active bank details found'], 404);
        }

        return (new SellerFinancialResource($financial))->additional(['success' => true]);
    }

    /**
     * Store or update financial details (Deleting old ones)
     */
    public function store(StoreSellerFinancialRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();

            // Force Delete existing details to avoid unique constraint violation
            UserSellerFinancial::withTrashed()->where('user_id', $user->id)->forceDelete();

            $financial = UserSellerFinancial::create(array_merge(
                $request->validated(),
                ['user_id' => $user->id, 'is_active' => true]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Bank details saved successfully.',
            ], 201);
        });
    }

    /**
     * Delete financial details
     */
    public function destroy(Request $request): JsonResponse
    {
        UserSellerFinancial::withTrashed()->where('user_id', $request->user()->id)->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Bank details deleted successfully',
        ]);
    }
}
