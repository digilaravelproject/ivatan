<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminLog;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserController
 * Manages User CRUD, Blocking, Verification, and Status Toggles.
 *
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of users with search and filtering.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $status = $request->query('status');
            $perPage = (int) $request->query('per_page', 15);

            $query = User::query();

            // Status Filters
            if ($status === 'active') {
                $query->where('is_blocked', 0)->where('status', 'active');
            } elseif ($status === 'inactive') {
                $query->where('status', 'inactive');
            } elseif ($status === 'blocked') {
                $query->where('is_blocked', 1);
            } elseif ($status === 'verified') {
                $query->where('is_verified', 1);
            }

            // Search Logic
            if ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            }

            $users = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

            return view('admin.users.index', [
                'users' => $users,
                'q' => $q,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading user index: " . $e->getMessage());
            return back()->with('error', 'Unable to load users list.');
        }
    }

    /**
     * Show the user profile.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        try {
            $user->load([
                'posts' => fn($q) => $q->latest()->limit(5),
                'reels' => fn($q) => $q->latest()->limit(5),
                'products' => fn($q) => $q->latest()->limit(5),
            ]);

            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            Log::error("Error loading user profile {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Unable to load user profile.');
        }
    }

    /**
     * Block a user.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();

            $user->is_blocked = 1;
            $user->save();

            $this->logAdminAction('block', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Account Suspended',
                    'message'     => 'Your account has been suspended by the admin.',
                    'action'      => 'blocked',
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Block notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', 'User blocked successfully.');
        } catch (\Exception $e) {
            Log::error("Error blocking user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Action failed.');
        }
    }

    /**
     * Unblock a user.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblock(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();

            $user->is_blocked = 0;
            $user->save();

            $this->logAdminAction('unblock', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Account Reinstated',
                    'message'     => 'Your account has been reinstated by the admin.',
                    'action'      => 'unblocked',
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Unblock notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', 'User unblocked successfully.');
        } catch (\Exception $e) {
            Log::error("Error unblocking user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Action failed.');
        }
    }

    /**
     * Verify a user.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user->is_verified = 1;
            $user->save();
            $this->logAdminAction('verify', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Account Verified',
                    'message'     => 'Your account has been verified by the admin.',
                    'action'      => 'verified',
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Verify notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', 'User verified successfully.');
        } catch (\Exception $e) {
            Log::error("Error verifying user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to verify user.');
        }
    }

    /**
     * Unverify a user.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unverify(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user->is_verified = 0;
            $user->save();
            $this->logAdminAction('unverify', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Verification Removed',
                    'message'     => 'Your account verification has been removed by the admin.',
                    'action'      => 'unverified',
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Unverify notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', 'User unverified successfully.');
        } catch (\Exception $e) {
            Log::error("Error unverifying user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to unverify user.');
        }
    }

    /**
     * Soft delete a user (Move to Trash).
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user->delete();
            $this->logAdminAction('Move to Trash', $user, $request);
            return redirect()->route('admin.users.index')->with('success', 'User moved to trash.');
        } catch (\Exception $e) {
            Log::error("Error deleting user {$user->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to delete user.');
        }
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();
            $this->logAdminAction('restore', $user, $request);
            return redirect()->route('admin.users.index')->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            Log::error("Error restoring user {$id}: " . $e->getMessage());
            return back()->with('error', 'Failed to restore user.');
        }
    }

    /**
     * Permanently delete a user.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user = User::withTrashed()->findOrFail($id);

            // Cleanup Media before deletion
            // Checks if it is NOT an external URL before deleting from storage
            if ($user->profile_photo_path && !filter_var($user->profile_photo_path, FILTER_VALIDATE_URL)) {
                // ✅ Using the correct config for disk
                Storage::disk(config('filesystems.default', 'public'))->delete($user->profile_photo_path);
            }

            // Clear Spatie Media
            $user->clearMediaCollection('profile_photo');

            $user->forceDelete();
            $this->logAdminAction('forceDelete', $user, $request);

            return redirect()->route('admin.users.index')->with('success', 'User permanently deleted.');
        } catch (\Exception $e) {
            Log::error("Error force deleting user {$id}: " . $e->getMessage());
            return back()->with('error', 'Failed to delete user permanently.');
        }
    }

    /**
     * View trashed users.
     */
    public function trashed(Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $users = User::onlyTrashed()->paginate(20);
            return view('admin.users.trashed', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to load trashed users.');
        }
    }

    public function toggleSellerStatus(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user->is_seller = !$user->is_seller;
            $user->save();
            $this->logAdminAction('toggle_seller_status', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Seller Status Updated',
                    'message'     => 'Your seller status has been updated to ' . ($user->is_seller ? 'enabled' : 'disabled') . ' by admin.',
                    'action'      => 'seller_' . ($user->is_seller ? 'enabled' : 'disabled'),
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Seller toggle notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', $user->is_seller ? "{$user->name} is now a seller." : "{$user->name} is no longer a seller.");
        } catch (\Exception $e) {
            return back()->with('error', 'Action failed.');
        }
    }

    public function toggleEmployerStatus(User $user, Request $request)
    {
        try {
            $this->authorizeAdminAction();
            $user->is_employer = !$user->is_employer;
            $user->save();
            $this->logAdminAction('toggle_Employer_status', $user, $request);

            try {
                $this->notificationService->sendToUser($user, 'admin_action', [
                    'title'       => 'Employer Status Updated',
                    'message'     => 'Your employer status has been updated to ' . ($user->is_employer ? 'enabled' : 'disabled') . ' by admin.',
                    'action'      => 'employer_' . ($user->is_employer ? 'enabled' : 'disabled'),
                    'action_url'  => null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Employer toggle notification failed for user {$user->id}: " . $e->getMessage());
            }

            return back()->with('success', $user->is_employer ? "{$user->name} is now an Employer." : "{$user->name} is no longer an Employer.");
        } catch (\Exception $e) {
            return back()->with('error', 'Action failed.');
        }
    }

    // --- Helpers ---

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
            Log::error('AdminLog failed: ' . $e->getMessage());
        }
    }

    /**
     * Check permissions using Auth Facade to fix Intelephense warnings.
     */
    protected function authorizeAdminAction()
    {
        // ✅ Uses Auth::check() and Auth::user() which helps IDE autocompletion
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }
}
