<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Ecommerce\StoreEnquiryRequest;
use App\Http\Requests\Ecommerce\UpdateEnquiryStatusRequest;
use App\Http\Resources\Ecommerce\EnquiryResource;
use App\Services\Ecommerce\EnquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnquiryController extends Controller
{
    protected EnquiryService $enquiryService;

    public function __construct(EnquiryService $enquiryService)
    {
        $this->enquiryService = $enquiryService;
    }

    /**
     * Store a new enquiry
     */
    public function store(StoreEnquiryRequest $request): JsonResponse
    {
        try {
            $userId = auth('sanctum')->id();
            $enquiry = $this->enquiryService->storeEnquiry($request->validated(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry submitted successfully.',
                'data' => new EnquiryResource($enquiry)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Enquiry Submission Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to submit enquiry'], 500);
        }
    }

    /**
     * List enquiries made by the user
     */
    public function myEnquiries(Request $request)
    {
        try {
            $enquiries = $this->enquiryService->listMyEnquiries($request->user());
            return EnquiryResource::collection($enquiries)->additional(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Enquiry myEnquiries Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve enquiries'], 500);
        }
    }

    /**
     * List enquiries for the seller
     */
    public function index(Request $request)
    {
        try {
            $enquiries = $this->enquiryService->listSellerEnquiries($request->user());
            return EnquiryResource::collection($enquiries)->additional(['success' => true]);
        } catch (AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            Log::error('Enquiry list Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve enquiries'], 500);
        }
    }

    /**
     * Get enquiry dashboard stats
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $stats = $this->enquiryService->getStats($request->user());
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            Log::error('Enquiry stats Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve stats'], 500);
        }
    }

    /**
     * Update enquiry status
     */
    public function updateStatus(UpdateEnquiryStatusRequest $request, string $identifier): JsonResponse
    {
        try {
            $enquiry = $this->enquiryService->updateStatus($identifier, $request->validated(), $request->user());
            return response()->json([
                'success' => true,
                'message' => 'Enquiry status updated to ' . $request->status,
                'data' => new EnquiryResource($enquiry)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        } catch (\Throwable $e) {
            Log::error('Enquiry status update Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update enquiry status'], 500);
        }
    }

    /**
     * Archive/Delete enquiry
     */
    public function destroy(Request $request, string $identifier): JsonResponse
    {
        try {
            $this->enquiryService->deleteEnquiry($identifier, $request->user());
            return response()->json([
                'success' => true,
                'message' => 'Enquiry archived successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        } catch (\Throwable $e) {
            Log::error('Enquiry destroy Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to archive enquiry'], 500);
        }
    }
}
