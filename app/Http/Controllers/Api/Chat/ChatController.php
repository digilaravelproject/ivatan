<?php

namespace App\Http\Controllers\Api\Chat;

use App\Events\Chat\MessageRead;
use App\Events\Chat\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\AddParticipantsRequest;
use App\Http\Requests\Chat\CreateGroupChatRequest;
use App\Http\Requests\Chat\CreatePrivateChatRequest;
use App\Http\Requests\Chat\MarkReadRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 1. INBOX: List of Chats
     * Optimized response: No duplication. Private chats use 'receiver' object. Groups use 'name'.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch chats where user is a participant
        $chats = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with([
                'lastMessage.sender',
                'participants.user:id,name,username,profile_photo_path,is_online,last_seen_at', // Fetch only specific user columns
            ])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        // Transform data
        $formattedChats = $chats->getCollection()->map(function ($chat) use ($user) {

            $receiverData = null;
            $chatName = null; // Only for groups
            $chatImage = null; // Only for groups

            // --- 1. Handle Chat Info ---
            if ($chat->type === 'private') {
                // Find the 'other' participant
                $otherParticipant = $chat->participants->firstWhere('user_id', '!=', $user->id);
                $otherUser = $otherParticipant ? $otherParticipant->user : null;

                if ($otherUser) {
                    $receiverData = [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'username' => $otherUser->username,
                        'avatar' => $otherUser->profile_photo_path, // Helper or accessor for full URL
                        'is_online' => $otherUser->is_online ?? false,
                    ];
                }
            } else {
                // Group Chat
                $chatName = $chat->name;
                $chatImage = null; // Add logic if groups have avatars
            }

            // --- 2. Calculate Unread Count ---
            $myParticipant = $chat->participants->firstWhere('user_id', $user->id);
            $unreadCount = 0;

            if ($myParticipant && $chat->lastMessage) {
                $lastReadId = $myParticipant->last_read_message_id ?? 0;
                if ($chat->lastMessage->id > $lastReadId) {
                    $unreadCount = $chat->messages()->where('id', '>', $lastReadId)->count();
                }
            }

            // --- 3. Format Last Message ---
            $lastMsgData = null;
            if ($chat->lastMessage) {
                $senderName = ($chat->lastMessage->sender_id === $user->id)
                    ? 'You'
                    : ($chat->lastMessage->sender->name ?? 'Unknown');

                $lastMsgData = [
                    'content' => $chat->lastMessage->message_type === 'text'
                        ? Str::limit($chat->lastMessage->content, 50)
                        : '📷 Attachment',
                    'type' => $chat->lastMessage->message_type,
                    'created_at' => $chat->lastMessage->created_at,
                    'time_ago' => $chat->lastMessage->created_at->diffForHumans(null, true, true), // Short format (e.g. 5m)
                    'is_mine' => $chat->lastMessage->sender_id === $user->id,
                    'sender_name' => $senderName,
                ];
            }

            return [
                'chat_id' => $chat->id,
                'type' => $chat->type,

                // Group Info (Null if private)
                'group_name' => $chatName,
                'group_image' => $chatImage,

                // Private Chat Target (Null if group)
                'receiver' => $receiverData,

                // Common Data
                'unread_count' => $unreadCount,
                'last_message' => $lastMsgData,
                'updated_at' => $chat->last_message_at,
            ];
        });

        return $this->success([
            'chats' => $formattedChats,
            'pagination' => [
                'current_page' => $chats->currentPage(),
                'last_page' => $chats->lastPage(),
                'has_more' => $chats->hasMorePages(),
            ],
        ], 'Inbox fetched.');
    }

    /**
     * 2. SINGLE CHAT DETAILS
     */
    public function show($chatId, Request $request)
    {
        try {
            $chat = UserChat::with(['participants.user:id,name,username,profile_photo_path,is_online,last_seen_at'])->findOrFail($chatId);
            $user = $request->user();

            if (! $chat->participants->contains('user_id', $user->id)) {
                return $this->error('Unauthorized', 403);
            }

            $participants = $chat->participants->map(function ($p) {
                return [
                    'id' => $p->user->id,
                    'name' => $p->user->name,
                    'username' => $p->user->username,
                    'avatar' => $p->user->profile_photo_path,
                    'is_admin' => (bool) $p->is_admin,
                    'is_online' => $p->user->is_online ?? false,
                    'last_seen' => $p->user->last_seen_at,
                ];
            });

            return $this->success([
                'id' => $chat->id,
                'type' => $chat->type,
                'name' => $chat->name, // Group name (null for private usually)
                'participants' => $participants,
                'participants_count' => $participants->count(),
            ]);
        } catch (\Exception $e) {
            return $this->error('Chat not found.', 404);
        }
    }

    /**
     * 3. START PRIVATE CHAT
     */
    public function openPrivate(CreatePrivateChatRequest $request)
    {
        try {
            $user = $request->user();
            $otherUserId = $request->other_user_id;

            if ($user->id == $otherUserId) {
                return $this->error('Cannot chat with yourself.', 422);
            }

            // A. Check Existing Chat
            $chat = UserChat::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherUserId))
                ->first();

            if ($chat) {
                // Load minimal participant data for response
                $chat->load('participants.user:id,name,username,profile_photo_path');

                return $this->success(['chat' => $chat], 'Chat retrieved.');
            }

            // B. PRIVACY CHECK
            $otherUser = User::findOrFail($otherUserId);

            if ($otherUser->account_privacy !== 'public') {
                $isFollowing = DB::table('follows')
                    ->where('follower_id', $user->id)
                    ->where('following_id', $otherUserId)
                    ->exists();

                if (! $isFollowing) {
                    return $this->error('This account is private. You must follow them to send a message.', 403);
                }
            }

            // C. Create Chat
            DB::beginTransaction();
            $chat = UserChat::create(['type' => 'private', 'last_message_at' => now()]);

            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $otherUserId]);
            DB::commit();

            $chat->load('participants.user:id,name,username,profile_photo_path');

            return $this->success(['chat' => $chat], 'Chat created.', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 4. CREATE GROUP CHAT
     */
    public function createGroup(CreateGroupChatRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $chat = UserChat::create([
                'type' => 'group',
                'name' => $request->name,
                'owner_id' => $user->id,
                'last_message_at' => now(),
            ]);

            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id, 'is_admin' => true]);

            foreach ($request->member_ids as $id) {
                if ($id != $user->id) {
                    UserChatParticipant::firstOrCreate(['chat_id' => $chat->id, 'user_id' => $id]);
                }
            }
            DB::commit();

            return $this->success(['chat' => $chat->load('participants.user:id,name,username,profile_photo_path')], 'Group created.', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Failed to create group.', 500);
        }
    }

    /**
     * 5. ADD PARTICIPANTS
     */
    public function addParticipants(AddParticipantsRequest $request, UserChat $chat)
    {
        $this->authorize('addParticipant', $chat);
        if ($chat->type !== 'group') {
            return $this->error('Not a group chat.');
        }

        $newIds = [];
        foreach ($request->member_ids as $id) {
            UserChatParticipant::firstOrCreate(['chat_id' => $chat->id, 'user_id' => $id]);
            $newIds[] = $id;
        }

        $chat->load('participants.user:id,name,username,profile_photo_path');

        return $this->success(['chat' => $chat, 'added_ids' => $newIds], 'Participants added.');
    }

    /**
     * 6. REMOVE PARTICIPANT
     */
    public function removeParticipant(UserChat $chat, $userId, Request $request)
    {
        $user = $request->user();

        $isOwner = $chat->owner_id === $user->id;
        $isAdmin = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->exists();

        if (! $isOwner && ! $isAdmin) {
            return $this->error('Unauthorized.', 403);
        }
        if ($userId == $chat->owner_id) {
            return $this->error('Cannot remove owner.', 422);
        }

        UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $userId)->delete();

        return $this->success([], 'Participant removed.');
    }

    /**
     * 7. LEAVE OR REMOVE
     */
    public function leaveOrRemove(UserChat $chat, Request $request)
    {
        $user = $request->user();
        $action = $request->input('action');

        if ($action === 'leave') {
            if ($chat->owner_id === $user->id) {
                return $this->error('Owner cannot leave. Transfer ownership first.');
            }
            UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->delete();

            return $this->success([], 'You left the group.');
        }

        if ($action === 'remove') {
            if ($chat->owner_id !== $user->id) {
                return $this->error('Only owner can bulk remove.');
            }
            $ids = $request->input('member_ids', []);
            if (empty($ids)) {
                return $this->error('No members selected.');
            }
            UserChatParticipant::where('chat_id', $chat->id)->whereIn('user_id', $ids)->delete();

            return $this->success([], 'Members removed.');
        }

        return $this->error('Invalid action.');
    }

    /**
     * 8. SEND MESSAGE
     */
    public function sendMessage(SendMessageRequest $request, UserChat $chat)
    {
        $user = $request->user();

        if (! $chat->participants()->where('user_id', $user->id)->exists()) {
            return $this->error('Unauthorized.', 403);
        }

        try {
            $attachmentPath = null;
            $meta = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $name = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs("chat_attachments/{$chat->id}", $name, 'public');
                $meta = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getClientMimeType(),
                ];
            }

            $message = UserChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'content' => $request->content,
                'message_type' => $request->message_type ?? 'text',
                'attachment_path' => $attachmentPath,
                'meta' => $meta,
                'reply_to_message_id' => $request->reply_to_message_id,
                'delivered_at' => now(),
            ]);

            $chat->touch('last_message_at');

            broadcast(new MessageSent($message))->toOthers();
            $this->sendNotifications($chat, $message, $user);

            return $this->success(['message' => $message], 'Message sent.', 201);
        } catch (\Exception $e) {
            \Log::error($e);

            return $this->error('Failed to send message.', 500);
        }
    }

    /**
     * 9. FETCH MESSAGES
     */
    public function messages(UserChat $chat, Request $request)
    {
        $user = $request->user();

        if (! $chat->participants()->where('user_id', $user->id)->exists()) {
            return $this->error('Unauthorized.', 403);
        }

        $perPage = $request->query('per_page', 50);

        $messages = $chat->messages()
            ->with(['sender:id,name,username,profile_photo_path', 'replyTo'])
            ->latest()
            ->cursorPaginate($perPage);

        $other = $chat->type === 'private'
            ? $chat->participants->firstWhere('user_id', '!=', $user->id)
            : null;

        $messages->getCollection()->transform(function ($msg) use ($other) {
            $msg->attachment_url = $msg->attachment_path ? url('/storage/' . $msg->attachment_path) : null;
            $msg->status = ($other && $other->last_read_message_id >= $msg->id) ? 'read' : 'sent';

            return $msg;
        });

        return $this->success($messages, 'Messages fetched.');
    }

    /**
     * 10. MARK READ
     */
    public function markRead(MarkReadRequest $request, UserChat $chat)
    {
        $user = $request->user();
        $lastId = $request->last_read_message_id;

        $participant = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $participant) {
            return $this->error('Unauthorized.', 403);
        }

        if ($lastId > $participant->last_read_message_id) {
            $participant->last_read_message_id = $lastId;
            $participant->save();
            broadcast(new MessageRead($chat->id, $user->id, $lastId))->toOthers();
        }

        return $this->success([], 'Marked as read.');
    }

    protected function sendNotifications($chat, $message, $sender)
    {
        $recipients = $chat->participants()
            ->where('user_id', '!=', $sender->id)
            ->pluck('user_id');

        $users = User::whereIn('id', $recipients)->get();

        foreach ($users as $user) {
            try {
                $this->notificationService->sendToUser($user, 'chat_message', [
                    'chat_id' => $chat->id,
                    'sender_name' => $sender->name,
                    'content' => $message->message_type === 'text' ? $message->content : 'Attachment',
                ]);
            } catch (\Throwable $e) {
            }
        }
    }
}
