<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobApplicationRequest;
use App\Models\Jobs\UserJobApplication;
use App\Models\Jobs\UserJobPost;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $application = DB::transaction(function () use ($job, $user, $request) {
                // Update or Create the Profile permanently
                $profile = \App\Models\Jobs\UserProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $request->only(['resume_headline', 'skills_list', 'contact_no'])
                );

                // Update Profile Employments (delete old, create new)
                if ($request->has('employments') && is_array($request->employments)) {
                    $profile->employments()->delete();
                    foreach ($request->employments as $emp) {
                        $profile->employments()->create($emp);
                    }
                }

                // Update Profile Educations (delete old, create new)
                if ($request->has('educations') && is_array($request->educations)) {
                    $profile->educations()->delete();
                    foreach ($request->educations as $edu) {
                        $profile->educations()->create($edu);
                    }
                }

                $appData = [
                    'job_id' => $job->id,
                    'applicant_id' => $user->id,
                    'cover_message' => $request->cover_message,
                    'resume_headline' => $profile->resume_headline,
                    'skills_list' => $profile->skills_list,
                    'contact_no' => $profile->contact_no,
                ];

                $app = UserJobApplication::create($appData);

                // Snapshot Employments and Educations to the Application
                foreach ($profile->employments as $emp) {
                    $app->employments()->create($emp->toArray());
                }
                foreach ($profile->educations as $edu) {
                    $app->educations()->create($edu->toArray());
                }

                // Handle optional resume upload
                if ($request->hasFile('resume')) {
                    $path = $request->file('resume')->store("resumes/{$user->id}", 'local');
                    if (!$path) {
                        throw new \Exception('Failed to upload resume to local storage.');
                    }
                    $app->resume_path = $path;
                    $app->save();
                }

                return $app;
            });

            return $this->success(
                $application->load([
                    'job:id,title,slug,company_name',
                    'applicant:id,name,username,email,profile_photo_path',
                    'employments',
                    'educations'
                ]),
                'Application submitted successfully',
                201
            );
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

            $applications = $job->applications()
                ->select('user_job_applications.*')
                ->with('applicant')
                ->leftJoin('user_subscriptions', function ($join) {
                    $join->on('user_subscriptions.user_id', '=', 'user_job_applications.applicant_id')
                        ->where('user_subscriptions.status', '=', 'active')
                        ->where(function ($q) {
                            $q->whereNull('user_subscriptions.ends_at')
                              ->orWhere('user_subscriptions.ends_at', '>', now());
                        });
                })
                ->leftJoin('plan_features', function ($join) {
                    $join->on('plan_features.subscription_plan_id', '=', 'user_subscriptions.subscription_plan_id')
                        ->whereIn('plan_features.feature_id', function ($q) {
                            $q->select('id')->from('features')->where('slug', 'job_priority');
                        });
                })
                ->orderByRaw('CAST(COALESCE(plan_features.limit_value, 0) AS UNSIGNED) DESC')
                ->orderBy('user_job_applications.created_at', 'desc')
                ->paginate(20);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('The given data was invalid.', 422, $e->errors());
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

            // Add secure resume URL if exists
            $applications->getCollection()->transform(function ($app) {
                $app->resume_url = $app->resume_path ? url("/api/v1/jobs/applications/{$app->id}/resume") : null;
                return $app;
            });

            return $this->success($applications, 'Your applications fetched successfully');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch your applications');
        }
    }

    /**
     * Get the logged-in user's career profile
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $profile = \App\Models\Jobs\UserProfile::with(['employments', 'educations'])
                ->firstOrCreate(['user_id' => $user->id]);

            return $this->success($profile, 'User career profile fetched successfully');
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to fetch user profile');
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

            // Support legacy public disk resumes as well
            $diskName = Storage::disk('local')->exists($application->resume_path) ? 'local' : 'public';

            $storageDisk = Storage::disk($diskName);
            assert($storageDisk instanceof \Illuminate\Filesystem\FilesystemAdapter);

            if (!$application->resume_path || !$storageDisk->exists($application->resume_path)) {
                return $this->error('Resume not found', 404);
            }

            return $storageDisk->download(
                $application->resume_path,
                $application->applicant->name . '_resume.pdf'
            );
        } catch (Throwable $e) {
            return $this->exceptionResponse($e, 'Failed to download resume');
        }
    }
}
