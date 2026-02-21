<?php
namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Jobs\UserJobPost;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    // List all job posts
    public function index(Request $request)
    {
        $jobs = UserJobPost::with('employer')->latest()->paginate(20);
        return view('admin.jobs.index', compact('jobs'));
    }

    // Show details of a single job
    public function show(UserJobPost $job)
    {
        $job->load('employer');
        return view('admin.jobs.show', compact('job'));
    }

    // Edit job post
    public function edit(UserJobPost $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    // Update job post
    public function update(Request $request, UserJobPost $job)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'location'         => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:255',
            'employment_type'  => 'nullable|string',
            'salary_min'       => 'nullable|numeric',
            'salary_max'       => 'nullable|numeric',
            'description'      => 'nullable|string',
            'status'           => 'required|in:published,draft,expired',
        ]);

        $job->update($validated);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    // Delete job post
    public function destroy(UserJobPost $job)
    {
        if ($job->company_logo) {
            \Storage::disk('public')->delete($job->company_logo);
        }

        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
