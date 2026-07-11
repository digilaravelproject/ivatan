<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveContentEnablement;
use App\Models\ExclusiveContentPurchase;
use App\Models\UserPost;
use App\Services\ExclusiveContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExclusiveContentController extends Controller
{
    public function __construct(protected ExclusiveContentService $service) {}

    // ---------------------------------------------------------
    // ENABLEMENT MANAGEMENT
    // ---------------------------------------------------------

    public function listEnablementRequests(): JsonResponse
    {
        $requests = ExclusiveContentEnablement::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($requests);
    }

    public function approveEnablement(Request $request, $id): JsonResponse
    {
        $enablement = ExclusiveContentEnablement::findOrFail($id);
        $enablement->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'override_platform_fee' => $request->override_platform_fee,
            'override_platform_fee_type' => $request->override_platform_fee_type,
        ]);

        return response()->json(['message' => 'Creator enablement approved.']);
    }

    public function rejectEnablement(Request $request, $id): JsonResponse
    {
        $request->validate(['admin_notes' => 'required|string']);
        $enablement = ExclusiveContentEnablement::findOrFail($id);
        $enablement->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json(['message' => 'Creator enablement rejected.']);
    }

    // ---------------------------------------------------------
    // CONTENT APPROVAL
    // ---------------------------------------------------------

    public function listPendingContent(): JsonResponse
    {
        $posts = UserPost::exclusive()->where('exclusive_status', 'pending')
            ->with(['user', 'media'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($posts);
    }

    public function approveContent(Request $request, $id): JsonResponse
    {
        $post = UserPost::exclusive()->findOrFail($id);
        $post->update([
            'exclusive_status' => 'approved',
            'override_platform_fee' => $request->override_platform_fee,
            'override_platform_fee_type' => $request->override_platform_fee_type,
        ]);

        return response()->json(['message' => 'Content approved.']);
    }

    public function rejectContent(Request $request, $id): JsonResponse
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $post = UserPost::exclusive()->findOrFail($id);
        $post->update([
            'exclusive_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'Content rejected.']);
    }

    // ---------------------------------------------------------
    // REFUNDS & TRANSACTIONS
    // ---------------------------------------------------------

    public function issueRefund(Request $request, $purchaseId): JsonResponse
    {
        $purchase = ExclusiveContentPurchase::findOrFail($purchaseId);
        $admin = auth()->guard('admin')->user();

        try {
            $this->service->issueRefund($purchase, $admin);
            return response()->json(['message' => 'Refund issued successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
