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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ChatController
 * Handles Chat Listing, Messaging, Group Management, and Real-time events.
 */
class ChatController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Helper to get authenticated user.
     */
    private function getAuthUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * List all chats (Inbox).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            $chats = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->with([
                    'lastMessage.sender',
                    'participants.user:id,name,username,profile_photo_path,is_online,last_seen_at',
                ])
                ->orderByDesc('last_message_at')
                ->paginate(20);

            $formattedChats = $chats->getCollection()->map(function ($chat) use ($user) {
                $receiverData = null;
                $chatName = $chat->name;
                $chatImage = null;

                // Handle Private vs Group Display Info
                if ($chat->type === 'private') {
                    $otherParticipant = $chat->participants->firstWhere('user_id', '!=', $user->id);
                    $otherUser = $otherParticipant ? $otherParticipant->user : null;

                    if ($otherUser) {
                        $receiverData = [
                            'id' => $otherUser->id,
                            'name' => $otherUser->name,
                            'username' => $otherUser->username,
                            'avatar' => $otherUser->profile_photo_path,
                            'is_online' => (bool) $otherUser->is_online,
                        ];
                    }
                }

                // Calculate Unread Count
                $myParticipant = $chat->participants->firstWhere('user_id', $user->id);
                $unreadCount = 0;
                if ($myParticipant && $chat->lastMessage) {
                    $lastReadId = $myParticipant->last_read_message_id ?? 0;
                    if ($chat->lastMessage->id > $lastReadId) {
                        $unreadCount = $chat->messages()->where('id', '>', $lastReadId)->count();
                    }
                }

                // Format Last Message
                $lastMsgData = null;
                if ($chat->lastMessage) {
                    $lastMsgData = [
                        'content' => $chat->lastMessage->message_type === 'text'
                            ? Str::limit($chat->lastMessage->content, 50)
                            : '📷 Attachment',
                        'type' => $chat->lastMessage->message_type,
                        'created_at' => $chat->lastMessage->created_at,
                        'time_ago' => $chat->lastMessage->created_at->diffForHumans(null, true, true),
                        'is_mine' => $chat->lastMessage->sender_id === $user->id,
                    ];
                }

                return [
                    'chat_id' => $chat->id,
                    'type' => $chat->type,
                    'group_name' => $chatName,
                    'receiver' => $receiverData,
                    'unread_count' => $unreadCount,
                    'last_message' => $lastMsgData,
                    'updated_at' => $chat->last_message_at,
                ];
            });

            return $this->success([
                'chats' => $formattedChats,
                'pagination' => [
                    'current_page' => $chats->currentPage(),
                    'has_more' => $chats->hasMorePages(),
                ],
            ], 'Inbox fetched.');
        } catch (\Exception $e) {
            Log::error("Inbox Error: " . $e->getMessage());
            return $this->error('Failed to load inbox.', 500);
        }
    }

    /**
     * Fetch messages for a specific chat.
     * Includes 'is_mine', 'status' (read/sent) and simplified sender info.
     *
     * @param UserChat $chat
     * @param Request $request
     * @return JsonResponse
     */
    public function messages(UserChat $chat, Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            // 1. Authorization Check
            $participant = UserChatParticipant::where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$participant) {
                return $this->error('Unauthorized.', 403);
            }

            // 2. Fetch Messages
            $perPage = $request->query('per_page', 50);
            $messages = $chat->messages()
                ->with(['sender:id,name,username,profile_photo_path', 'replyTo'])
                ->latest()
                ->cursorPaginate($perPage);

            // 3. Auto Mark as Read Logic
            $latestMessage = $chat->messages()->latest('id')->first();
            if ($latestMessage && $latestMessage->id > $participant->last_read_message_id) {
                $participant->last_read_message_id = $latestMessage->id;
                $participant->save();
                broadcast(new MessageRead($chat->id, $user->id, $latestMessage->id))->toOthers();
            }

            // 4. Identify Other Participant (For Read Status logic in Private Chats)
            $otherParticipant = null;
            if ($chat->type === 'private') {
                $otherParticipant = $chat->participants->where('user_id', '!=', $user->id)->first();
            }

            // 5. Transform Data
            $formattedMessages = $messages->getCollection()->map(function ($msg) use ($user, $otherParticipant) {

                // Determine Status (Blue Tick Logic)
                $status = 'sent';
                if ($otherParticipant && $otherParticipant->last_read_message_id >= $msg->id) {
                    $status = 'read';
                }

                return [
                    'id' => $msg->id,
                    'chat_id' => $msg->chat_id,
                    'content' => $msg->content,
                    'message_type' => $msg->message_type,
                    'attachment_path' => $msg->attachment_path,
                    'attachment_url' => $msg->attachment_path ? url('/storage/' . $msg->attachment_path) : null,
                    'is_mine' => $msg->sender_id === $user->id, // Frontend uses this for alignment
                    'status' => $status, // sent | read
                    // 'meta' => $msg->meta,
                    'created_at' => $msg->created_at,
                    'sender' => [
                        'id' => $msg->sender->id,
                        'name' => $msg->sender->name,
                        'avatar' => $msg->sender->profile_photo_path,
                    ],
                    'reply_to' => $msg->replyTo ? [
                        'id' => $msg->replyTo->id,
                        'content' => Str::limit($msg->replyTo->content, 30),
                        'sender_name' => $msg->replyTo->sender->name ?? 'Unknown'
                    ] : null
                ];
            });

            // 6. Return Clean JSON
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $formattedMessages,
                    'next_cursor' => $messages->nextCursor() ? $messages->nextCursor()->encode() : null,
                    'prev_cursor' => $messages->previousCursor() ? $messages->previousCursor()->encode() : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Fetch Messages Error: " . $e->getMessage());
            return $this->error('Failed to load messages.', 500);
        }
    }

    /**
     * Send a new message.
     *
     * @param SendMessageRequest $request
     * @param UserChat $chat
     * @return JsonResponse
     */
    public function sendMessage(SendMessageRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $user = $this->getAuthUser();

            if (!$chat->participants()->where('user_id', $user->id)->exists()) {
                return $this->error('You are not a participant.', 403);
            }

            // Handle Attachment
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

            // Create Message
            $message = UserChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'content' => $request->input('content'),
                'message_type' => $request->input('message_type', 'text'),
                'attachment_path' => $attachmentPath,
                'meta' => $meta,
                'reply_to_message_id' => $request->input('reply_to_message_id'),
                'delivered_at' => now(),
            ]);

            $chat->touch('last_message_at');

            // Broadcast & Notify
            broadcast(new MessageSent($message))->toOthers();
            $this->sendNotifications($chat, $message, $user);

            // Format Response to match listing structure
            $message->load('sender:id,name,username,profile_photo_path');

            return $this->success([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'is_mine' => true,
                    'status' => 'sent',
                    'created_at' => $message->created_at,
                    'attachment_url' => $message->attachment_path ? url('/storage/' . $message->attachment_path) : null,
                    'sender' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->profile_photo_path
                    ]
                ]
            ], 'Message sent.', 201);
        } catch (\Exception $e) {
            Log::error("Send Message Error: " . $e->getMessage());
            return $this->error('Failed to send message.', 500);
        }
    }

    /**
     * Start or Retrieve a Private Chat.
     *
     * @param CreatePrivateChatRequest $request
     * @return JsonResponse
     */
    public function openPrivate(CreatePrivateChatRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $otherUserId = $request->input('other_user_id');

            if ($user->id == $otherUserId) return $this->error('Cannot chat with yourself.', 422);

            // Check Existing
            $chat = UserChat::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherUserId))
                ->first();

            if ($chat) return $this->success(['chat' => $chat], 'Chat retrieved.');

            // Create New
            DB::beginTransaction();
            $chat = UserChat::create(['type' => 'private', 'last_message_at' => now()]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $otherUserId]);
            DB::commit();

            return $this->success(['chat' => $chat], 'Chat created.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Open Private Error: " . $e->getMessage());
            return $this->error('Failed to initiate chat.', 500);
        }
    }

    /**
     * Mark messages as read manually (Socket Trigger).
     *
     * @param MarkReadRequest $request
     * @param UserChat $chat
     * @return JsonResponse
     */
    public function markRead(MarkReadRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $user = $this->getAuthUser();
            $lastId = $request->input('last_read_message_id');

            $participant = UserChatParticipant::where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->first();

            if ($participant && $lastId > $participant->last_read_message_id) {
                $participant->last_read_message_id = $lastId;
                $participant->save();
                broadcast(new MessageRead($chat->id, $user->id, $lastId))->toOthers();
            }

            return $this->success([], 'Marked as read.');
        } catch (\Exception $e) {
            return $this->error('Failed to mark read.', 500);
        }
    }

    /**
     * Send notifications to other participants.
     */
    protected function sendNotifications($chat, $message, $sender)
    {
        try {
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
                } catch (\Throwable $t) {
                    Log::warning("Notification failed for user {$user->id}: " . $t->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Notification Service Error: " . $e->getMessage());
        }
    }
}
