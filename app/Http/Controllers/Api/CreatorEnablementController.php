<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveContentEnablement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorEnablementController extends Controller
{
    public function __construct(
        protected \App\Services\Payment\PaymentOrchestrator $paymentOrchestrator
    ) {}

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
            'payment_status' => $enablement ? $enablement->payment_status : 'none',
        ]);
    }

    /**
     * Request Enablement and initiate payment if fee is required.
     */
    public function requestEnablement(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            // Read enablement fee from Settings or default to 0.
            $globalFee = (float) (\App\Models\Setting::where('key', 'exclusive_content_enablement_fee')->value('value') ?? 0);
            
            if ($globalFee > 0) {
                // Create or update the enablement record as pending payment and call gateway inside a transaction
                $paymentResponse = \Illuminate\Support\Facades\DB::transaction(function () use ($user, $globalFee) {
                    $enablement = ExclusiveContentEnablement::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'fee_paid' => $globalFee,
                            'status' => 'pending',
                            'payment_status' => 'pending',
                        ]
                    );

                    return $this->paymentOrchestrator->createEnablementPayment($enablement, $user);
                });

                return response()->json($paymentResponse);
            } else {
                // Free enablement - set payment status directly to completed inside transaction
                $enablement = \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                    return ExclusiveContentEnablement::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'fee_paid' => 0,
                            'status' => 'pending',
                            'payment_status' => 'completed',
                        ]
                    );
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Enablement requested. Waiting for admin approval.',
                    'data' => $enablement
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Request Enablement Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to request enablement.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Enablement Payment.
     */
    public function verifyEnablement(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'gateway_payload' => 'required|array',
            ]);

            $user = Auth::guard('sanctum')->user();

            // Retrieve the record with lockForUpdate to prevent race conditions during verification
            $enablement = \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                return ExclusiveContentEnablement::where('user_id', $user->id)->lockForUpdate()->first();
            });

            if (!$enablement) {
                return response()->json(['message' => 'No pending enablement request found.'], 404);
            }

            if ($enablement->payment_status === 'completed') {
                return response()->json(['message' => 'Payment already completed.', 'success' => true]);
            }

            $result = $this->paymentOrchestrator->verifyEnablementPayment($enablement, $request->gateway_payload);

            if (!$result->success) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($enablement) {
                    $enablement->update(['payment_status' => 'failed']);
                });
                return response()->json(['message' => 'Payment verification failed.', 'error' => $result->message], 400);
            }

            // Payment is successful, update inside transaction
            \Illuminate\Support\Facades\DB::transaction(function () use ($enablement) {
                $enablement->update([
                    'payment_status' => 'completed',
                    'status' => 'pending', // Pending admin approval
                ]);
            });

            return response()->json(['message' => 'Payment successful. Enablement requested.', 'success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Verify Enablement Payment Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to verify payment.', 'message' => $e->getMessage()], 500);
        }
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

        return response()->json([
            'success' => true,
            'is_enabled' => (bool) $request->is_enabled,
            'message' => $message
        ]);
    }
}
