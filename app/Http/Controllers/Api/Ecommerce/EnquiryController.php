<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Ecommerce\StoreEnquiryRequest;
use App\Http\Requests\Ecommerce\UpdateEnquiryStatusRequest;
use App\Http\Resources\Ecommerce\EnquiryResource;
use App\Models\Ecommerce\UserEnquiry;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class EnquiryController extends Controller
{
    /**
     * Store a new enquiry (Rate-limited via Route Middleware)
     */
    public function store(StoreEnquiryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Link to authenticated user if logged in
            if (auth('sanctum')->check()) {
                $data['user_id'] = auth('sanctum')->id();
            }

            $enquiry = UserEnquiry::create($data);
            $enquiry->load(['service', 'product', 'seller']);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry submitted successfully.',
                'data' => new EnquiryResource($enquiry)
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Enquiry Submission Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to submit enquiry'], 500);
        }
    }

    /**
     * List enquiries made by the user
     */
    public function myEnquiries(Request $request): AnonymousResourceCollection
    {
        $enquiries = UserEnquiry::with(['service', 'product', 'seller'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return EnquiryResource::collection($enquiries)->additional(['success' => true]);
    }

    /**
     * List enquiries for the seller
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = $request->user();
        if (!$user->is_seller) {
            return response()->json(['success' => false, 'message' => 'Only sellers can view enquiries'], 403);
        }

        $enquiries = UserEnquiry::with(['service', 'product', 'seller', 'user'])
            ->where('seller_id', $user->id)
            ->latest()
            ->paginate(15);

        return EnquiryResource::collection($enquiries)->additional(['success' => true]);
    }

    /**
     * Get enquiry dashboard stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->is_seller) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total' => UserEnquiry::where('seller_id', $user->id)->count(),
            'pending' => UserEnquiry::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'replied' => UserEnquiry::where('seller_id', $user->id)->where('status', 'replied')->count(),
            'closed' => UserEnquiry::where('seller_id', $user->id)->where('status', 'closed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Update enquiry status (and optionally message)
     */
    public function updateStatus(UpdateEnquiryStatusRequest $request, string $identifier): JsonResponse
    {
        $enquiry = UserEnquiry::with('user')
            ->where(function($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('uuid', $identifier);
            })->firstOrFail();

        // Security check: Only the seller of THIS enquiry can update it
        if ($enquiry->seller_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $enquiry->update([
            'status' => $request->status,
            'reply_message' => $request->reply_message ?? $enquiry->reply_message,
        ]);

        // Notify the user who made the enquiry
        if ($enquiry->user) {
            app(NotificationService::class)->sendToUser(
                $enquiry->user,
                'enquiry_update',
                [
                    'enquiry_id' => $enquiry->id,
                    'status' => $request->status,
                    'message' => "Your enquiry status has been updated to {$request->status}.",
                    'reply' => $request->reply_message ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Enquiry status updated to ' . $request->status,
            'data' => new EnquiryResource($enquiry)
        ]);
    }

    /**
     * Archive/Delete enquiry
     */
    public function destroy(Request $request, string $identifier): JsonResponse
    {
        $enquiry = UserEnquiry::where(function($query) use ($identifier) {
                $query->where('id', $identifier)
                      ->orWhere('uuid', $identifier);
            })->firstOrFail();

        if ($enquiry->seller_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $enquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry archived successfully'
        ]);
    }
}
