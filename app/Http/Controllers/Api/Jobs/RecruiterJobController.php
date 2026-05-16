<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Jobs\UserJobPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecruiterJobController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of jobs posted by the logged-in recruiter.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user->is_employer) {
                return $this->error('Only employers can access recruiter dashboard.', 403);
            }

            $query = UserJobPost::where('employer_id', $user->id)
                ->withCount('applications');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $jobs = $query->orderBy('created_at', 'desc')->paginate((int) $request->query('per_page', 20));

            return $this->success($jobs, 'Recruiter jobs fetched successfully.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch recruiter jobs.');
        }
    }

    /**
     * Update job status (Open/Closed).
     */
    public function updateStatus(Request $request, UserJobPost $job): JsonResponse
    {
        try {
            $user = $request->user();

            if ($job->employer_id !== $user->id) {
                return $this->error('Unauthorized to update this job status.', 403);
            }

            $request->validate([
                'status' => 'required|in:draft,published,closed'
            ]);

            $job->update(['status' => $request->status]);

            return $this->success($job, 'Job status updated successfully.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to update job status.');
        }
    }
}
