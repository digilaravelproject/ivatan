<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Users list with search, filters and pagination.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $status = $request->query('status'); // active | inactive | blocked | verified

        $perPage = (int) $request->query('per_page', 15);

        $query = User::query();

        // Filters
        if ($status === 'active') {
            $query->where('is_blocked', 0)->where('status', 'active');
        } elseif ($status === 'inactive') {
            $query->where('status', 'inactive');
        } elseif ($status === 'blocked') {
            $query->where('is_blocked', 1);
        } elseif ($status === 'verified') {
            $query->where('is_verified', 1);
        }

        // Search: if Scout/Searchable is configured it will be used, otherwise fallback to DB LIKE.
        $useScout = false;
        try {
            $useScout = in_array(\Laravel\Scout\Searchable::class, class_uses(User::class) ?: []);
        } catch (\Throwable $e) {
            $useScout = false;
        }

        if ($q) {
            if ($useScout) {
                // Scout paginates itself if driver supports
                $users = User::search($q)->paginate($perPage);
            } else {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
                $users = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
            }
        } else {
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        }

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'status' => $status,
        ]);
    }

    /**
     * Show user profile + recent content skeleton.
     */
    public function show(User $user)
    {
        // load recent content (if those relations exist)
        $user->load([
            'posts' => function ($q) {
                $q->latest()->limit(5);
            },
            'reels' => function ($q) {
                $q->latest()->limit(5);
            },
            'products' => function ($q) {
                $q->latest()->limit(5);
            },
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Block user.
     */
    public function block(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_blocked = 1;
        $user->save();

        $this->logAdminAction('block', $user, $request);

        return back()->with('success', 'User blocked successfully.');
    }

    /**
     * Unblock user.
     */
    public function unblock(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_blocked = 0;
        $user->save();

        $this->logAdminAction('unblock', $user, $request);

        return back()->with('success', 'User unblocked successfully.');
    }

    /**
     * Verify user (set is_verified = 1).
     */
    public function verify(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_verified = 1;
        $user->save();

        $this->logAdminAction('verify', $user, $request);

        return back()->with('success', 'User verified successfully.');
    }
    public function unverify(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_verified = 0;
        $user->save();

        $this->logAdminAction('unverify', $user, $request);

        return back()->with('success', 'User Unverified successfully.');
    }

    /**
     * Soft delete user.
     */
    public function destroy(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->delete(); // assumes SoftDeletes on User model

        $this->logAdminAction('Move to Trash', $user, $request);

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function restore($id, Request $request)
    {
        $this->authorizeAdminAction();

        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        $this->logAdminAction('restore', $user, $request);

        return redirect()->route('admin.users.index')->with('success', 'User restored successfully.');
    }


    public function forceDelete($id, Request $request)
    {
        $this->authorizeAdminAction();

        // Use findOrFail to manually fetch the user
        $user = User::withTrashed()->findOrFail($id);

        $user->forceDelete();

        $this->logAdminAction('forceDelete', $user, $request);

        return redirect()->route('admin.users.index')->with('success', 'User permanently deleted.');
    }




    /**
     * Helper to create admin audit log.
     */
    protected function logAdminAction(string $action, User $targetUser, Request $request): void
    {
        try {
            AdminLog::create([
                'admin_id' => Auth::id(),
                'action' => $action,
                'target_type' => 'user',
                'target_id' => $targetUser->id,
                'payload' => json_encode([
                    'user_snapshot' => $targetUser->toArray(),
                    'note' => $request->input('note') ?? null,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // don't break flow on logging errors (but you can log to laravel log)
            \Log::error('AdminLog failed: ' . $e->getMessage());
        }
    }

    /**
     * Quick guard helper (you can replace with Spatie/Policies).
     */
    protected function authorizeAdminAction()
    {

        if (! auth()->check() || ! auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    public function trashed(Request $request)
    {
        $this->authorizeAdminAction();
        $users = User::onlyTrashed()->paginate(20);
        return view('admin.users.trashed', compact('users'));
    }

    public function toggleSellerStatus(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_seller = !$user->is_seller;
        $user->save();

        $this->logAdminAction('toggle_seller_status', $user, $request);

        $message = $user->is_seller
            ? "{$user->name} is now a seller."
            : "{$user->name} is no longer a seller.";

        return back()->with('success', $message);
    }
    public function toggleEmployerStatus(User $user, Request $request)
    {
        $this->authorizeAdminAction();

        $user->is_employer = !$user->is_employer;
        $user->save();

        $this->logAdminAction('toggle_Employer_status', $user, $request);

        $message = $user->is_employer
            ? "{$user->name} is now an Employer."
            : "{$user->name} is no longer an Employer.";

        return back()->with('success', $message);
    }
}
