<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveContentPurchase;
use App\Models\UserPost;
use App\Services\ExclusiveContentService;
use App\Services\Payment\PaymentOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExclusiveContentPurchaseController extends Controller
{
    public function __construct(
        protected ExclusiveContentService $exclusiveContentService,
        protected PaymentOrchestrator $paymentOrchestrator
    ) {}

    /**
     * Initiate Purchase Flow.
     */
    public function initiate(Request $request, UserPost $post): JsonResponse
    {
        try {
            $user = auth()->guard('sanctum')->user();

            if (!$post->is_exclusive || $post->exclusive_status !== 'approved') {
                return response()->json(['message' => 'This content is not available for purchase.'], 400);
            }

            if ($user->hasExclusiveAccessTo($post->id)) {
                return response()->json(['message' => 'You already have active access to this content.'], 400);
            }

            // Block check
            if ($user->hasBlockRelationWith($post->user)) {
                return response()->json(['message' => 'You cannot purchase this content.'], 403);
            }

            // Calculate price
            $breakdown = $this->exclusiveContentService->calculatePurchaseBreakdown($post, $post->user);

            $purchase = DB::transaction(function () use ($user, $post, $breakdown) {
                return ExclusiveContentPurchase::create([
                    'buyer_id' => $user->id,
                    'user_post_id' => $post->id,
                    'creator_price' => $breakdown['creator_price'],
                    'platform_fee_charged' => $breakdown['platform_fee'],
                    'gateway_charge_amount' => $breakdown['gateway_charge'],
                    'gateway_charge_bearer' => $breakdown['gateway_charge_bearer'],
                    'final_paid_amount' => $breakdown['final_price'],
                    'status' => 'pending',
                ]);
            });

            // Call gateway to create payment intent
            $paymentResponse = $this->paymentOrchestrator->createExclusiveContentPayment($purchase, $user);

            return response()->json($paymentResponse);
        } catch (\Exception $e) {
            Log::error("Initiate Exclusive Purchase Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to initiate purchase.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Purchase from Gateway.
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'purchase_id' => 'required|exists:exclusive_content_purchases,id',
                'gateway_payload' => 'required|array',
            ]);

            $purchase = ExclusiveContentPurchase::findOrFail($request->purchase_id);
            $user = auth()->guard('sanctum')->user();

            if ($purchase->buyer_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }

            if ($purchase->status === 'completed') {
                return response()->json(['message' => 'Payment already completed.', 'success' => true]);
            }

            $result = $this->paymentOrchestrator->verifyExclusiveContentPayment($purchase, $request->gateway_payload);

            if (!$result->success) {
                $purchase->update(['status' => 'failed']);
                return response()->json(['message' => 'Payment verification failed.', 'error' => $result->message], 400);
            }

            // Process purchase logic (grant access, distribute funds)
            $this->exclusiveContentService->processSuccessfulPurchase($purchase);

            return response()->json(['message' => 'Payment successful. Content unlocked.', 'success' => true]);
        } catch (\Exception $e) {
            Log::error("Verify Exclusive Purchase Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to verify payment.'], 500);
        }
    }
}
