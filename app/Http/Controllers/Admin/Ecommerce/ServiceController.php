<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserService;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show list of products with optional status filter and search.
     */
    public function index(Request $request)
    {
        $query = UserService::with(['seller']);

        // filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // simple search on title, sku or uuid (if you have sku)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('uuid', 'like', "%{$q}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.service.index', compact('services'));
    }
    public function show(Request $request, UserService $service)
    {
        $service->load('images', 'seller'); // Eager load relationships

        return view('admin.service.show', compact('service'));
    }


    /**
     * Approve a pending product
     */
    public function approve(UserService $service, Request $request)
    {
        $service->status = 'approved';
        $service->admin_note = $service->admin_note ?? null;
        $service->save();

        try {
            $seller = User::find($service->seller_id);
            if ($seller) {
                $this->notificationService->sendToUser($seller, 'content_approved', [
                    'title'       => 'Service Approved',
                    'message'     => "Your service \"{$service->title}\" has been approved and is now live.",
                    'target_type' => 'service',
                    'target_id'   => $service->id,
                    'action_url'  => null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Service approval notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Service approved successfully.');
    }

    public function reject(UserService $userService, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $userService->status = 'rejected';
        $userService->admin_note = $request->admin_note;
        $userService->save();

        try {
            $seller = User::find($userService->seller_id);
            if ($seller) {
                $this->notificationService->sendToUser($seller, 'content_rejected', [
                    'title'       => 'Service Rejected',
                    'message'     => "Your service \"{$userService->title}\" has been rejected.",
                    'reason'      => $request->admin_note,
                    'target_type' => 'service',
                    'target_id'   => $userService->id,
                    'action_url'  => null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Service rejection notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Service rejected and seller notified.');
    }
}
