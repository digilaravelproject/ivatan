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
        $request->validate([
            'override_platform_fee_type' => 'nullable|string|in:flat,percentage',
            'override_platform_fee' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $enablement = ExclusiveContentEnablement::findOrFail($id);

        if ($enablement->fee_paid > 0 && $enablement->payment_status !== 'completed') {
            return response()->json(['message' => 'Cannot approve an unpaid enablement request.'], 400);
        }

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

        $posts->getCollection()->transform(function ($post) {
            $post->images = $post->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'mime_type' => $media->mime_type,
                ];
            });

            $post->videos = $post->getMedia('videos')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'mime_type' => $media->mime_type,
                ];
            });

            // Calculate and attach active platform fee breakdown
            if ($post->user) {
                $post->platform_fee_breakdown = $this->service->calculatePurchaseBreakdown($post, $post->user);
            } else {
                $post->platform_fee_breakdown = null;
            }

            return $post;
        });

        return response()->json($posts);
    }

    public function listApprovedContent(): JsonResponse
    {
        $posts = UserPost::exclusive()->where('exclusive_status', 'approved')
            ->with(['user', 'media'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $posts->getCollection()->transform(function ($post) {
            $post->images = $post->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'mime_type' => $media->mime_type,
                ];
            });

            $post->videos = $post->getMedia('videos')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'mime_type' => $media->mime_type,
                ];
            });

            // Calculate and attach active platform fee breakdown
            if ($post->user) {
                $post->platform_fee_breakdown = $this->service->calculatePurchaseBreakdown($post, $post->user);
            } else {
                $post->platform_fee_breakdown = null;
            }

            return $post;
        });

        return response()->json($posts);
    }

    public function approveContent(Request $request, $id): JsonResponse
    {
        $request->validate([
            'override_platform_fee_type' => 'nullable|string|in:flat,percentage',
            'override_platform_fee' => 'nullable|numeric|min:0',
        ]);

        $post = UserPost::exclusive()->findOrFail($id);

        $feeType = $request->override_platform_fee_type;
        $feeValue = $request->override_platform_fee;

        if (empty($feeType)) {
            $feeType = null;
            $feeValue = null;
        }

        $post->update([
            'exclusive_status' => 'approved',
            'override_platform_fee' => $feeValue,
            'override_platform_fee_type' => $feeType,
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

    // ---------------------------------------------------------
    // SETTINGS MANAGEMENT
    // ---------------------------------------------------------

    public function getSettings(): JsonResponse
    {
        return response()->json([
            'exclusive_content_enablement_fee' => \App\Models\Setting::where('key', 'exclusive_content_enablement_fee')->value('value') ?? 999,
            'exclusive_content_global_fee_type' => \App\Models\Setting::where('key', 'exclusive_content_global_fee_type')->value('value') ?? 'percentage',
            'exclusive_content_global_fee_value' => \App\Models\Setting::where('key', 'exclusive_content_global_fee_value')->value('value') ?? 2,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'exclusive_content_enablement_fee' => 'required|numeric|min:0',
            'exclusive_content_global_fee_type' => 'required|string|in:flat,percentage',
            'exclusive_content_global_fee_value' => 'required|numeric|min:0',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'exclusive_content_enablement_fee'],
            ['value' => $request->exclusive_content_enablement_fee, 'group' => 'exclusive', 'type' => 'integer']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'exclusive_content_global_fee_type'],
            ['value' => $request->exclusive_content_global_fee_type, 'group' => 'exclusive', 'type' => 'string']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'exclusive_content_global_fee_value'],
            ['value' => $request->exclusive_content_global_fee_value, 'group' => 'exclusive', 'type' => 'integer']
        );

        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return response()->json(['success' => true, 'message' => 'Settings updated successfully.']);
    }
}
