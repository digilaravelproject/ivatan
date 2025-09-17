<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\MarkReadRequest;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected NotificationService $svc;

    public function __construct(NotificationService $svc)
    {
        $this->svc = $svc;
    }

    /**
     * GET /api/notifications
     * List paginated notifications.
     * Query Parameters:
     * - only: unread|all (default: all)
     * - per_page: number of items per page (default: 20)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $only = $request->query('only', 'all');
        $perPage = (int) $request->query('per_page', 20);

        $query = $user->notifications()->orderByDesc('created_at');

        if ($only === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($perPage);

        $notifications->getCollection()->transform(function ($notification) {
            return [
                'id'         => $notification->id,
                'type'       => $notification->type,
                'data'       => $notification->data,
                'read_at'    => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * GET /api/notifications/unread-count
     * Get the total unread notification count.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'unread' => $count,
        ]);
    }

    /**
     * POST /api/notifications/mark-read
     * Mark a specific notification as read.
     * Body: { notification_id: string }
     */
    public function markRead(MarkReadRequest $request)
    {
        $user = $request->user();
        $notificationId = $request->notification_id;

        DB::transaction(function () use ($user, $notificationId) {
            $notification = $user->notifications()
                ->where('id', $notificationId)
                ->lockForUpdate()
                ->first();

            if (! $notification) {
                throw new ModelNotFoundException('Notification not found.');
            }

            // Only mark and update count if unread
            if (is_null($notification->read_at)) {
                $notification->markAsRead();

                $row = DB::table('notification_unread_counts')
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($row && $row->unread_count > 0) {
                    DB::table('notification_unread_counts')
                        ->where('user_id', $user->id)
                        ->decrement('unread_count');
                }
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/notifications/mark-all-read
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            $user->unreadNotifications->each(fn($notification) => $notification->markAsRead());

            DB::table('notification_unread_counts')->updateOrInsert(
                ['user_id' => $user->id],
                ['unread_count' => 0, 'updated_at' => now()]
            );
        });

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/notifications/send-test
     * Send a test notification to a user (admin/debug route).
     * Body: { "user_id": 5, "category": "test", "payload": {...} }
     */
    public function sendTest(Request $request)
    {
        // $this->authorize('sendNotifications', User::class);

        $data = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'category' => 'required|string|max:100',
            'payload'  => 'nullable|array',
        ]);

        $user = User::find($data['user_id']);

        $this->svc->sendToUser(
            $user,
            $data['category'],
            $data['payload'] ?? []
        );

        return response()->json(['success' => true]);
    }
}
