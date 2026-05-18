<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GenericNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = DB::table('notifications')
            ->select([
                'notifications.id',
                'notifications.type',
                'notifications.data',
                'notifications.read_at',
                'notifications.created_at',
                'notifications.notifiable_id',
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->join('users', 'notifications.notifiable_id', '=', 'users.id')
            ->orderByDesc('notifications.created_at');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($category = $request->category) {
            $query->where('notifications.data', 'like', '%"category":"' . $category . '"%');
        }

        if ($request->only === 'unread') {
            $query->whereNull('notifications.read_at');
        }

        $notifications = $query->paginate(20);

        $notifications->getCollection()->transform(function ($n) {
            $data = json_decode($n->data, true);
            return [
                'id'            => $n->id,
                'user_name'     => $n->user_name,
                'user_email'    => $n->user_email,
                'user_id'       => $n->notifiable_id,
                'category'      => $data['category'] ?? 'unknown',
                'title'         => $data['payload']['title'] ?? 'Notification',
                'message'       => $data['payload']['message'] ?? '',
                'read_at'       => $n->read_at,
                'created_at'    => $n->created_at,
            ];
        });

        $categories = array_keys(config('notifications.categories', []));

        return view('admin.notifications.index', compact('notifications', 'categories'));
    }

    public function show(string $id)
    {
        $notification = DB::table('notifications')
            ->select([
                'notifications.*',
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->join('users', 'notifications.notifiable_id', '=', 'users.id')
            ->where('notifications.id', $id)
            ->firstOrFail();

        $data = json_decode($notification->data, true);

        return view('admin.notifications.show', [
            'notification' => $notification,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $categories = array_keys(config('notifications.categories', []));
        return view('admin.notifications.send', compact('categories'));
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'category' => 'required|string|max:100',
            'title'    => 'required|string|max:255',
            'message'  => 'required|string|max:5000',
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            $this->notificationService->sendToUser($user, $request->category, [
                'title'      => $request->title,
                'message'    => $request->message,
                'action_url' => $request->action_url,
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', "Notification sent to {$user->name}.");
        } catch (\Exception $e) {
            Log::error('Admin send notification failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send notification.')->withInput();
        }
    }

    public function unreadCount()
    {
        $admin = Auth::user();
        $count = DB::table('notification_unread_counts')
            ->where('user_id', $admin->id)
            ->value('unread_count') ?? 0;

        return response()->json(['count' => (int) $count]);
    }

    public function recent()
    {
        $notifications = DB::table('notifications')
            ->select(['id', 'data', 'read_at', 'created_at', 'notifiable_id'])
            ->where('notifiable_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $items = $notifications->map(function ($n) {
            $data = json_decode($n->data, true);
            return [
                'id'      => $n->id,
                'title'   => $data['payload']['title'] ?? 'Notification',
                'message' => $data['payload']['message'] ?? '',
                'read_at' => $n->read_at,
                'time'    => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
            ];
        });

        return response()->json(['notifications' => $items]);
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'title'    => 'required|string|max:255',
            'message'  => 'required|string|max:5000',
        ]);

        try {
            $users = User::where('is_blocked', 0)->get();

            foreach ($users as $user) {
                try {
                    $this->notificationService->sendToUser($user, $request->category, [
                        'title'      => $request->title,
                        'message'    => $request->message,
                        'action_url' => $request->action_url,
                    ]);
                } catch (\Throwable $e) {
                    Log::error("Broadcast notification failed for user {$user->id}: " . $e->getMessage());
                }
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', "Broadcast sent to {$users->count()} users.");
        } catch (\Exception $e) {
            Log::error('Admin broadcast notification failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send broadcast.')->withInput();
        }
    }
}
