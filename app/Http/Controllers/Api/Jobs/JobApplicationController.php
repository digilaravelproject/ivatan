<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobApplicationRequest;
use App\Models\Jobs\UserJobApplication;
use App\Models\Jobs\UserJobPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class JobApplicationController extends Controller
{
    use ApiResponse;

    /**
     * Apply for a job
     * @param Request $request
     * @param StoreJobApplicationRequest $request
     * @param int $jobId
     * @return JsonResponse
     */
    public function apply(StoreJobApplicationRequest $request, $jobId): JsonResponse
    {
        try {
            $user = $request->user();

            $job = UserJobPost::findOrFail($jobId);

            // Prevent employer from applying
            if ($user->is_employer) {
                return $this->error('Employers cannot apply for jobs', 403);
            }

            // Prevent duplicate applications
            if (UserJobApplication::where('job_id', $job->id)->where('applicant_id', $user->id)->exists()) {
                return $this->error('You have already applied for this job', 422);
            }

            $application = UserJobApplication::create([
                'job_id' => $job->id,
                'applicant_id' => $user->id,
                'cover_message' => $request->cover_message,
            ]);

            // Handle resume upload
            if ($request->hasFile('resume')) {
                try {
                    $path = $request->file('resume')->store("resumes/{$user->id}", 'public');
                    $application->resume_path = $path;
                    $application->save();
                } catch (Throwable $e) {
                    \Log::warning('Resume upload failed: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'job_id' => $job->id,
                    ]);
                }
            }

            return $this->success($application, 'Application submitted successfully', 201);
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to apply for job');
        }
    }

    /**
     * List applications for a specific job (employer only)
     * @param UserJobPost $job
     * @param Request $request
     * @return JsonResponse
     */
    public function listByJob(UserJobPost $job, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($job->employer_id !== $user->id) {
                return $this->error('Unauthorized to view applications', 403);
            }

            $applications = $job->applications()->with('applicant')->paginate(20);

            return $this->success($applications, 'Applications fetched successfully');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch applications');
        }
    }

    /**
     * Update application status (employer only)
     * @param UserJobApplication $application
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(UserJobApplication $application, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $job = $application->job;

            if ($job->employer_id !== $user->id) {
                return $this->error('Unauthorized to update application status', 403);
            }

            $request->validate([
                'status' => 'required|in:applied,viewed,shortlisted,rejected,hired'
            ]);

            $application->status = $request->status;
            $application->save();

            return $this->success($application, 'Application status updated successfully');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to update application status');
        }
    }

    /**
     * List current user's applications
     * @param Request $request
     * @return JsonResponse
     */
    public function myApplications(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $query = UserJobApplication::where('applicant_id', $user->id)
                ->with([
                    'job' => function ($q) {
                        $q->select('id', 'uuid', 'title', 'company_name', 'employer_id', 'slug');
                    },
                    'job.employer' => function ($q) {
                        $q->select('id', 'name');
                    }
                ]);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $perPage = (int) $request->query('per_page', 20);

            $applications = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Add resume URL if exists
            $applications->getCollection()->transform(function ($app) {
                $app->resume_url = $app->resume_path ? Storage::disk('public')->url($app->resume_path) : null;
                return $app;
            });

            return $this->success($applications, 'Your applications fetched successfully');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch your applications');
        }
    }

    /**
     * Download resume securely (applicant or employer only)
     * @param UserJobApplication $application
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
     * @throws Throwable
     */
    public function downloadResume(UserJobApplication $application, Request $request): JsonResponse|StreamedResponse
    {
        try {
            $user = $request->user();
            $job = $application->job;

            if ($job->employer_id !== $user->id && $application->applicant_id !== $user->id) {
                return $this->error('Unauthorized to download resume', 403);
            }

            if (!$application->resume_path || !Storage::disk('public')->exists($application->resume_path)) {
                return $this->error('Resume not found', 404);
            }

            return Storage::disk('public')->download(
                $application->resume_path,
                $application->applicant->name . '_resume.pdf'
            );
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to download resume');
        }
    }
}
