<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

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
        $service->admin_note = $service->admin_note ?? null; // keep existing note if any
        $service->save();

        // optional: dispatch notification job to seller

        return redirect()->back()->with('success', 'Service approved successfully.');
    }

    /**
     * Reject product with admin note
     */
    public function reject(UserService $userService, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $userService->status = 'rejected';
        $userService->admin_note = $request->admin_note;
        $userService->save();

        // optional: notify seller about rejection and note

        return redirect()->back()->with('success', 'Service rejected and seller notified.');
    }
}
