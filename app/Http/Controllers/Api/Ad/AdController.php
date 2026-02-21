<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Models\Ad;
use App\Models\AdPackage;
use App\Models\Interest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdController extends Controller
{
    use AuthorizesRequests;
    // public function __construct()
    // {
    //     // Apply auth middleware for all routes
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * List all ad packages (cached)
     */
    public function adPackages(): JsonResponse
    {
        try {
            $packages = Cache::remember('ad_packages', now()->addMinutes(30), fn() => AdPackage::all());

            return response()->json(['success' => true, 'packages' => $packages], 200);
        } catch (\Exception $e) {
            Log::error('AdPackages Fetch Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Unable to fetch ad packages'], 500);
        }
    }

    /**
     * Store a new ad
     */
    public function store(StoreAdRequest $request): JsonResponse
    {
        /** @var \Illuminate\Http\Request $request */
        try {
            $user = $request->user();

            // Authorization: only verified users or admin
            $this->authorize('create', Ad::class);

            $package = AdPackage::findOrFail($request->ad_package_id);
            $interest = Interest::findOrFail($request->interest_id);

            $startAt = $request->start_type === 'scheduled' && $request->start_at
                ? Carbon::parse($request->start_at)
                : null; // auto-set after admin approval if not scheduled

            // Create the ad first
            $ad = Ad::create([
                'user_id'       => $user->id,
                'ad_package_id' => $package->id,
                'title'         => $request->title,
                'description'   => $request->description,
                // 'interest_id'   => $interest->id,
                'status'        => 'pending_admin_approval',
                'start_at'      => $startAt,
            ]);
            $ad->interests()->sync($request->interest_id);

            // Handle media using Spatie Media Library
            if ($request->hasFile('media')) {
                $allMediaIds = [];

                foreach ($request->file('media') as $file) {
                    $media = $ad->addMedia($file)->toMediaCollection('ads');
                    $allMediaIds[] = $media->id;
                }

                if (!empty($allMediaIds)) {
                    $ad->update(['media_ids' => $allMediaIds]);
                }
            }



            Cache::forget("user_ads_{$user->id}");

            return response()->json([
                'success' => true,
                'ad' => $ad->load('media') // eager load media relation if needed
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create an ad'
            ], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ad Package or Interest not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Ad Creation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create ad. Please try again later.'
            ], 500);
        }
    }


    /**
     * Show individual ad (creator/admin only)
     */
    public function show(Ad $ad): JsonResponse
    {
        try {
            $this->authorize('view', $ad);

            // Load related data
            $ad->load(['package', 'user', 'interests', 'media']);

            // Include media IDs and optional URLs
            $adData = $ad->toArray();
            $adData['media_ids'] = $ad->media_ids ?? [];
            $adData['media_urls'] = $ad->getMedia('ads')->map(fn($m) => $m->getUrl());

            return response()->json([
                'success' => true,
                'ad' => $adData
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this ad'
            ], 403);
        } catch (\Exception $e) {
            Log::error('Ad Fetch Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'ad_id' => $ad->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch ad details'
            ], 500);
        }
    }



    /**
     * List all ads for authenticated user (sorted: live → pending → expired)
     * Admin can fetch all ads using ?all=true
     */
    public function myAds(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $isAdmin = $user->is_admin;
            $cacheKey = $isAdmin && $request->query('all') ? "ads_all" : "user_ads_{$user->id}";

            $ads = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user, $isAdmin, $request) {
                $query = $isAdmin && $request->query('all') ? Ad::query() : Ad::where('user_id', $user->id);

                if ($request->query('status')) {
                    $query->where('status', $request->query('status'));
                }

                return $query->with(['package', 'interests', 'user', 'media'])
                    ->orderByRaw("
                    FIELD(status, 'live', 'pending_admin_approval', 'awaiting_payment', 'approved', 'rejected', 'expired') ASC
                ")
                    ->latest()
                    ->paginate(15);
            });

            // Add media URLs and IDs to each ad
            $ads->getCollection()->transform(function ($ad) {
                $ad->media_ids = $ad->media_ids ?? [];
                $ad->media_urls = $ad->getMedia('ads')->map(fn($m) => $m->getUrl());
                return $ad;
            });

            return response()->json([
                'success' => true,
                'ads' => $ads
            ], 200);
        } catch (\Exception $e) {
            Log::error('MyAds Fetch Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch ads'
            ], 500);
        }
    }



    /**
     * Admin approves ad → auto sets start and end dates
     */
    public function approve(Ad $ad): JsonResponse
    {
        try {
            $this->authorize('approve', $ad);

            $package = $ad->package;

            $startAt = $ad->start_at ?? Carbon::now();
            $endAt   = (clone $startAt)->addDays($package->duration_days);

            $ad->update([
                'status'   => 'live',
                'start_at' => $startAt,
                'end_at'   => $endAt,
            ]);

            Cache::forget("user_ads_{$ad->user_id}");
            Cache::forget("ads_all");

            return response()->json(['success' => true, 'ad' => $ad], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            Log::error('Ad Approval Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to approve ad'], 500);
        }
    }
}
