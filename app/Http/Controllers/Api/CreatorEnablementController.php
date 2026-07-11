<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveContentEnablement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorEnablementController extends Controller
{
    /**
     * Get current enablement status.
     */
    public function status(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $enablement = $user->enablement;

        return response()->json([
            'status' => $enablement ? $enablement->status : 'none',
            'fee_paid' => $enablement ? $enablement->fee_paid : 0,
        ]);
    }

    /**
     * Request Enablement and mock fee payment (in real app, use PaymentOrchestrator).
     */
    public function requestEnablement(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        // Mocking fee check. Real app would read from Setting.
        $globalFee = \App\Models\Setting::where('key', 'exclusive_content_enablement_fee')->value('value') ?? 999;
        
        $enablement = ExclusiveContentEnablement::updateOrCreate(
            ['user_id' => $user->id],
            [
                'fee_paid' => $globalFee,
                'status' => 'pending',
            ]
        );

        return response()->json([
            'message' => 'Enablement requested. Waiting for admin approval.',
            'data' => $enablement
        ]);
    }

    /**
     * Toggle Feature status (Creator side disable).
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['is_enabled' => 'required|boolean']);
        
        $user = Auth::guard('sanctum')->user();
        $enablement = $user->enablement;

        if (!$enablement || !in_array($enablement->status, ['approved', 'disabled_by_creator'])) {
            return response()->json(['message' => 'You cannot toggle the feature right now. Ensure it is approved first.'], 400);
        }

        if ($request->is_enabled) {
            $enablement->update(['status' => 'approved']);
            $message = 'Exclusive Content feature enabled.';
        } else {
            $enablement->update(['status' => 'disabled_by_creator']);
            $message = 'Exclusive Content feature disabled.';
        }

        return response()->json(['message' => $message]);
    }
}
