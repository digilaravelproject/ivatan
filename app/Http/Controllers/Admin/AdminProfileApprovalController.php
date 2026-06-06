<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileSwitchRequest;
use App\Services\Admin\ProfileApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminProfileApprovalController extends Controller
{
    public function __construct(
        protected ProfileApprovalService $profileApprovalService
    ) {}

    public function index(Request $request): View
    {
        $query = ProfileSwitchRequest::with([
            'user:id,name,email,username',
            'fromProfile',
            'toProfile',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('profile_type')) {
            $query->where('to_profile_type', $request->profile_type);
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'pending' => ProfileSwitchRequest::where('status', 'pending')->count(),
            'approved' => ProfileSwitchRequest::where('status', 'approved')->count(),
            'rejected' => ProfileSwitchRequest::where('status', 'rejected')->count(),
            'approved_today' => ProfileSwitchRequest::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'rejected_today' => ProfileSwitchRequest::where('status', 'rejected')
                ->whereDate('updated_at', today())->count(),
            'total' => ProfileSwitchRequest::count(),
        ];

        return view('admin.profile-approval.index', compact('requests', 'summary'));
    }

    public function show(int $id): View
    {
        $switchRequest = ProfileSwitchRequest::with([
            'user:id,name,email,username,phone',
            'fromProfile',
            'toProfile',
            'approver:id,name,email',
        ])->findOrFail($id);

        return view('admin.profile-approval.show', compact('switchRequest'));
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $switchRequest = ProfileSwitchRequest::findOrFail($id);

            if (!$switchRequest->isPending()) {
                return redirect()->back()->withErrors(['error' => "This request has already been {$switchRequest->status}."]);
            }

            $this->profileApprovalService->approve(
                $switchRequest,
                auth()->id(),
                $request->admin_notes
            );

            return redirect()->route('admin.profile-approval.index')
                ->with('success', 'Profile switch approved successfully. User notified.');
        } catch (\Throwable $e) {
            Log::error('Admin profile approval failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to approve: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $switchRequest = ProfileSwitchRequest::findOrFail($id);

            if (!$switchRequest->isPending()) {
                return redirect()->back()->withErrors(['error' => "This request has already been {$switchRequest->status}."]);
            }

            $this->profileApprovalService->reject(
                $switchRequest,
                auth()->id(),
                $request->admin_notes
            );

            return redirect()->route('admin.profile-approval.index')
                ->with('success', 'Profile switch request rejected.');
        } catch (\Throwable $e) {
            Log::error('Admin profile rejection failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to reject: ' . $e->getMessage()]);
        }
    }
}
