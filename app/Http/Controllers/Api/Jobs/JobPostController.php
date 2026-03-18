<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobPostRequest;
use App\Http\Requests\Jobs\UpdateJobPostRequest;
use App\Models\Jobs\UserJobPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use App\Jobs\TrackJobView;

class JobPostController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of published jobs with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = UserJobPost::query()->where('status', 'published');

            // Filters
            foreach (['title', 'location', 'country'] as $field) {
                if ($request->filled($field)) {
                    $query->where($field, 'like', '%' . $request->$field . '%');
                }
            }

            if ($request->filled('employment_type')) {
                $query->where('employment_type', $request->employment_type);
            }

            if ($request->filled('is_remote')) {
                $query->where('is_remote', (bool) $request->is_remote);
            }

            if ($request->filled('salary_min')) {
                $query->where('salary_min', '>=', $request->salary_min);
            }

            if ($request->filled('salary_max')) {
                $query->where('salary_max', '<=', $request->salary_max);
            }

            $user = Auth::user();
            $jobs = $query->with('employer')
                ->when($user, function ($q) use ($user) {
                    $q->withExists(['applications' => function ($aq) use ($user) {
                        $aq->where('applicant_id', $user->id);
                    }]);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return $this->success($jobs, 'Jobs fetched successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch job listings.');
        }
    }

    /**
     * Show a specific job.
     * @param \Illuminate\Http\Request $request
     * @param  mixed  $identifier  Job ID or slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($identifier): JsonResponse
    {
        try {
            $user = Auth::user();

            // Fetch job by ID or slug
            $job = UserJobPost::where('id', $identifier)
                ->orWhere('slug', $identifier)
                ->with('employer')
                ->when($user, function ($q) use ($user) {
                    $q->withExists(['applications' => function ($aq) use ($user) {
                        $aq->where('applicant_id', $user->id);
                    }]);
                })
                ->firstOrFail();

            $ipAddress = request()->ip();

            // Dispatch background job to handle view tracking
            TrackJobView::dispatch($job->id, $user?->id, $ipAddress);

            return $this->success($job, 'Job fetched successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch job details.');
        }
    }


    /**
     * Store a newly created job post.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Http\Requests\Jobs\StoreJobPostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(StoreJobPostRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Generate unique slug
            $slug = Str::slug($request->title)
                . '-' . Str::slug($request->company_name ?? 'company')
                . '-' . Str::random(10);

            $jobData = $request->validated();

            if (!empty($jobData['is_urgent']) && $jobData['is_urgent']) {
                $jobData['urgent_until'] = now()->addDays(14);
            }

            // Create job post
            $job = UserJobPost::create(array_merge(
                $jobData,
                [
                    'slug'        => $slug,
                    'employer_id' => $user->id,
                ]
            ));

            // Handle company logo upload
            if ($request->hasFile('company_logo')) {
                try {
                    $path = $request->file('company_logo')->store("company_logos/{$user->id}", 'public');
                    $job->update(['company_logo' => $path]);
                } catch (Throwable $e) {
                    Log::warning("Logo upload failed: " . $e->getMessage(), [
                        'user_id' => $user->id,
                        'job_id'  => $job->id,
                    ]);
                }
            }

            return $this->success(
                $job->load('employer'),
                'Job created successfully.',
                201
            );
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to create job post.');
        }
    }


    /**
     * Update an existing job post.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Http\Requests\Jobs\UpdateJobPostRequest  $request
     * @param  \App\Models\Jobs\UserJobPost  $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateJobPostRequest $request, UserJobPost $job): JsonResponse
    {
        try {
            $user = $request->user();
            $jobData = $request->validated();

            // Handle urgency logic
            if (isset($jobData['is_urgent'])) {
                if ($jobData['is_urgent'] && !$job->is_urgent) {
                    $jobData['urgent_until'] = now()->addDays(14);
                } elseif (!$jobData['is_urgent']) {
                    $jobData['urgent_until'] = null;
                }
            }

            // Handle logo upload (if any)
            if ($request->hasFile('company_logo')) {
                // Delete old logo
                if (!empty($job->company_logo)) {
                    Storage::disk('public')->delete($job->company_logo);
                }

                // Store new logo and add to data array
                $jobData['company_logo'] = $request->file('company_logo')->store("company_logos/{$user->id}", 'public');
            }

            // Single update call
            $job->update($jobData);

            return $this->success(
                $job->fresh('employer'),
                'Job updated successfully.'
            );
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to update job post.');
        }
    }

    /**
     * Delete a job.
     */
    public function destroy(UserJobPost $job, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($job->employer_id !== $user->id) {
                return $this->error('Unauthorized to delete this job.', 403);
            }

            if ($job->company_logo) {
                Storage::disk('public')->delete($job->company_logo);
            }

            $job->delete();

            return $this->success([], 'Job post deleted successfully.');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to delete job post.');
        }
    }
}
