<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Jobs\UserJobApplication;
use App\Models\Jobs\UserJobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminApplicationController extends Controller
{
    // List applications under a specific job
    public function listByJob(UserJobPost $job)
    {
        $applications = $job->applications()->with('applicant')->paginate(20);
        return view('admin.jobs.applications', compact('job', 'applications'));
    }

    // Download resume
    public function downloadResume(UserJobApplication $application)
    {
        if (!$application->resume_path || !Storage::disk('public')->exists($application->resume_path)) {
            return redirect()->back()->with('error', 'Resume not found.');
        }

        return Storage::disk('public')->download(
            $application->resume_path,
            $application->applicant->name . '_resume.pdf'
        );
    }
}
