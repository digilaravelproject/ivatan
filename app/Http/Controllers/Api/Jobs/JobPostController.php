<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobPostRequest;
use App\Models\Jobs\UserJobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobPostController extends Controller
{
    // List all published jobs with optional filters

    public function index(Request $request)
    {
        $query = UserJobPost::query()->where('status', 'published');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
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

        $jobs = $query->with('employer')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json(['success' => true, 'data' => $jobs]);
    }


    // Show single job post by ID
    public function show(UserJobPost $job)
    {
        $job->increment('views_count');
        return response()->json(['success' => true, 'data' => $job->load('employer')]);
    }

    // Create job post (employer only)
    public function store(StoreJobPostRequest $request)
    {
        $user = $request->user();
        // Generate a custom slug: title-companyName-random
        $slug = Str::slug($request->title) . '-' . Str::slug($request->company_name) . '-' . Str::random(10);

        // Creating the job post with the validated data
        $job = UserJobPost::create(array_merge(
            $request->validated(),
            [
                'slug'        => $slug,
                'employer_id' => $user->id,
            ]
        ));

        // Handle company logo upload if provided
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store("company_logos/{$user->id}", 'public');
            $job->company_logo = $path;
            $job->save();
        }

        return response()->json(['success' => true, 'job' => $job->load('employer')], 201);
    }

    // Update job post (employer only)
    public function update(StoreJobPostRequest $request, userJobPost $job)
    {
        $user = $request->user();
        if ($job->employer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $job->update($request->validated());

        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($job->company_logo) {
                \Storage::disk('public')->delete($job->company_logo);
            }
            $path = $request->file('company_logo')->store("company_logos/{$user->id}", 'public');
            $job->company_logo = $path;
            $job->save();
        }

        return response()->json(['success' => true, 'job' => $job->load('employer')]);
    }

    // Delete job post (employer only)
    public function destroy(UserJobPost $job, Request $request)
    {
        $user = $request->user();
        if ($job->employer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($job->company_logo) {
            \Storage::disk('public')->delete($job->company_logo);
        }

        $job->delete();
        return response()->json(['success' => true, 'message' => 'Job post deleted']);
    }
}
