<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveSwitchRequest;
use App\Models\ProfileSwitchRequest;
use App\Services\Admin\ProfileApprovalService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProfileApprovalController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProfileApprovalService $profileApprovalService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['profile_type', 'per_page']);
            $requests = $this->profileApprovalService->listPendingRequests($filters);

            return $this->success($requests, 'Pending switch requests retrieved successfully.');
        } catch (Throwable $e) {
            Log::error('Failed to fetch pending requests', ['error' => $e->getMessage()]);
            return $this->exceptionResponse($e, 'Failed to fetch pending requests.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $request = ProfileSwitchRequest::with([
                'user:id,name,email,username,phone',
                'fromProfile',
                'toProfile',
                'approver:id,name,email',
            ])->findOrFail($id);

            return $this->success(['switch_request' => $request], 'Switch request details retrieved successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Switch request not found.');
        }
    }

    public function approve(int $id, ApproveSwitchRequest $request): JsonResponse
    {
        try {
            $switchRequest = ProfileSwitchRequest::findOrFail($id);

            if (!$switchRequest->isPending()) {
                return $this->error(
                    "This request has already been {$switchRequest->status}.",
                    422
                );
            }

            $adminId = $request->user()->id;

            if ($request->status === 'approved') {
                $result = $this->profileApprovalService->approve(
                    $switchRequest,
                    $adminId,
                    $request->admin_notes
                );

                return $this->success($result, 'Profile switch approved successfully.');
            }

            $updatedRequest = $this->profileApprovalService->reject(
                $switchRequest,
                $adminId,
                $request->admin_notes
            );

            return $this->success([
                'switch_request' => $updatedRequest,
            ], 'Profile switch request rejected.');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            Log::error('Failed to process switch request', [
                'request_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return $this->exceptionResponse($e, 'Failed to process switch request.');
        }
    }
}
