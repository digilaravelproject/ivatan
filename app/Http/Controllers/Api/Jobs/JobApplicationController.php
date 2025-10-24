<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobApplicationRequest;
use App\Models\jobs\UserJobApplication;
use App\Models\Jobs\UserJobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    // Apply for a job
    public function apply(StoreJobApplicationRequest $request, $job)
    {
        $user = $request->user();

        $job = UserJobPost::findOrFail($job);

        // Prevent employer from applying
        if ($user->is_employer) {
            return response()->json(['error' => 'Employers cannot apply for jobs'], 403);
        }

        // Check for duplicate application
        if (UserJobApplication::where('job_id', $job->id)->where('applicant_id', $user->id)->exists()) {
            return response()->json(['error' => 'You have already applied for this job'], 422);
        }

        $application = UserJobApplication::create([
            'job_id' => $job->id,
            'applicant_id' => $user->id,
            'cover_message' => $request->cover_message,
        ]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store("resumes/{$user->id}", 'public');
            $application->resume_path = $path;
            $application->save();
        }

        return response()->json(['success' => true, 'application' => $application], 201);
    }

    // List applications for a specific job (employer only)
    public function listByJob(UserJobPost $job, Request $request)
    {
        $user = $request->user();
        if ($job->employer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $applications = $job->applications()->with('applicant')->paginate(20);
        return response()->json(['success' => true, 'applications' => $applications]);
    }

    // Update application status (employer only)
    public function updateStatus(UserJobApplication $application, Request $request)
    {
        $user = $request->user();
        $job = $application->job;

        if ($job->employer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:applied,viewed,shortlisted,rejected,hired'
        ]);

        $application->status = $request->status;
        $application->save();

        return response()->json(['success' => true, 'application' => $application]);
    }


    public function myApplications(Request $request): JsonResponse
    {
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

        // pagination size (optional query param ?per_page=50)
        $perPage = (int) $request->query('per_page', 20);

        $applications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Optionally add a resume_url for quick download (uses public disk).
        // If you want secure/signed URLs, replace this logic with a signed route.
        $applications->getCollection()->transform(function ($app) {
            if (!empty($app->resume_path)) {
                $app->resume_url = \Storage::disk('public')->url($app->resume_path);
            } else {
                $app->resume_url = null;
            }
            return $app;
        });

        return response()->json([
            'success' => true,
            'data' => $applications
        ], 200);
    }

    public function downloadResume(UserJobApplication $application, Request $request)
    {
        $user = $request->user();
        $job  = $application->job;

        // ✅ Only employer of the job OR the applicant himself can download
        if ($job->employer_id !== $user->id && $application->applicant_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // ✅ Check if resume exists
        if (!$application->resume_path || !\Storage::disk('public')->exists($application->resume_path)) {
            return response()->json(['error' => 'Resume not found'], 404);
        }

        // ✅ Download securely
        return \Storage::disk('public')->download(
            $application->resume_path,
            $application->applicant->name . '_resume.pdf' // nice readable filename
        );
    }
}
