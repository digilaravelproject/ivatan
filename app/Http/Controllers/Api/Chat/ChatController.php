<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\AddParticipantsRequest;
use App\Http\Requests\Chat\CreateGroupChatRequest;
use App\Http\Requests\Chat\CreatePrivateChatRequest;
use App\Http\Requests\Chat\MarkReadRequest;
use App\Http\Requests\Chat\MarkDeliveredRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Chat\MessageResource;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Services\ChatService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use ApiResponse;

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Inbox & Listing
     * Filters: 'groups', 'unread', 'read', 'business'
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $filter = $request->query('filter');

            // Base Query: User must be a participant
            $query = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->with(['participants.user', 'lastMessage.sender'])
                ->withCount(['participants']);

            // --- FILTER LOGIC ---

            if ($filter === 'live_groups') {
                $query->whereNotNull('live_chat_group_id');
            } else {
                $query->whereNull('live_chat_group_id');
            }

            if ($filter === 'groups') {
                // Show only Group chats
                $query->where('type', 'group');
            } 
            elseif ($filter === 'business') {
                // Show chats where the OTHER user is a Seller
                // (Hum check kar rahe hain ki participants mein koi aisa user hai jo seller hai aur main nahi hu)
                $query->whereHas('participants.user', function ($q) use ($user) {
                    $q->where('is_seller', true)
                      ->where('id', '!=', $user->id);
                });
            } 
            elseif ($filter === 'unread') {
                // Show chats having messages newer than what I have read
                $query->whereHas('lastMessage', function ($q) use ($user) {
                    $q->whereRaw('user_chat_messages.id > (
                        SELECT COALESCE(last_read_message_id, 0)
                        FROM user_chat_participants 
                        WHERE user_chat_participants.chat_id = user_chat_messages.chat_id 
                        AND user_chat_participants.user_id = ?
                    )', [$user->id]);
                });
            } 
            elseif ($filter === 'read') {
                // Show chats where I have read the last message OR chats with no messages
                $query->where(function ($mainQ) use ($user) {
                    // Condition A: Last message ID is <= my last_read_id
                    $mainQ->whereHas('lastMessage', function ($q) use ($user) {
                        $q->whereRaw('user_chat_messages.id <= (
                            SELECT COALESCE(last_read_message_id, 0)
                            FROM user_chat_participants 
                            WHERE user_chat_participants.chat_id = user_chat_messages.chat_id 
                            AND user_chat_participants.user_id = ?
                        )', [$user->id]);
                    })
                    // Condition B: Or chats that strictly have no unread messages (catch-all)
                    ->orWhereDoesntHave('lastMessage'); 
                });
            }

            // Sorting: Newest message first
            $chats = $query->orderByDesc('last_message_at')->simplePaginate(20);

            return $this->success([
                'chats' => ChatResource::collection($chats)->response()->getData(true)
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
    /**
     * 2. Chat Details
     */
    public function show(UserChat $chat): JsonResponse
    {
        try {
            $user = Auth::user();
            $isParticipant = $chat->participants()->where('user_id', $user->id)->exists();
            if (!$isParticipant) {
                return $this->error('Unauthorized.', 403);
            }
            return $this->success(new ChatResource($chat->load('participants.user')));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 3. Fetch Messages
     */
         public function messages(UserChat $chat, Request $request): JsonResponse
    {
        try {
            $query = $chat->messages()
                ->visibleToUser(Auth::id())
                ->with(['sender', 'replyTo.sender']);

            // ✅ LOGIC: Agar frontend last message ID bhejta hai (Polling)
            if ($request->has('after_id')) {
                $query->where('id', '>', $request->input('after_id'));
                
                // Polling ke time pagination nahi, direct naye messages chahiye
                $messages = $query->oldest()->get(); 
            } else {
                // First time load ke time pagination chahiye
                $messages = $query->latest()->cursorPaginate(30); 
            }

            return $this->success(MessageResource::collection($messages));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
    /**
     * 4. Open/Start Private Chat
     */
    public function openPrivate(CreatePrivateChatRequest $request): JsonResponse
    {
        try {
            $chat = $this->chatService->getPrivateChat(Auth::id(), $request->other_user_id);
            return $this->success(new ChatResource($chat));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 5. Create Group
     */
    public function createGroup(CreateGroupChatRequest $request): JsonResponse
    {
        try {
            $chat = $this->chatService->createGroup(
                Auth::id(),
                $request->name,
                $request->participant_ids,
                $request->file('avatar')
            );
            return $this->success(new ChatResource($chat), 'Group created', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 6. Add Participants to Group
     */
    public function addParticipants(AddParticipantsRequest $request, UserChat $chat): JsonResponse
    {
        if ($chat->type !== 'group') {
            return $this->error('Cannot add participants to a private chat.', 400);
        }

        try {
            $this->chatService->addParticipants(Auth::user(), $chat, $request->member_ids);
            return $this->success(new ChatResource($chat->load('participants.user')), 'Members added.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 7. Leave Group or Remove Member
     */
    public function leaveGroup(Request $request, UserChat $chat): JsonResponse
    {
        if ($chat->type !== 'group') {
            return $this->error('Cannot leave a private chat.', 400);
        }

        // Check who is the target. If 'user_id' is passed, we try to remove them.
        // If 'user_id' is NOT passed, we remove the current logged-in user (Leave).
        $targetUserId = $request->input('user_id', Auth::id());

        try {
            $this->chatService->leaveChat(Auth::user(), $chat, $targetUserId);

            $msg = ($targetUserId === Auth::id()) ? 'You left the group.' : 'Member removed.';
            return $this->success(null, $msg);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 8. Send Message
     */
    public function sendMessage(SendMessageRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $message = $this->chatService->sendMessage(Auth::user(), $chat, $request->validated());
            return $this->success(new MessageResource($message), 'Sent', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 9. Mark as Read
     */
    public function markRead(MarkReadRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $this->chatService->markAsRead(
                Auth::user(),
                $chat->id,
                $request->validated()['last_read_message_id']
            );

            return $this->success(null, 'Messages marked as read.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 9b. Mark as Delivered
     */
    public function markDelivered(MarkDeliveredRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $this->chatService->markAsDelivered(
                Auth::user(),
                $chat->id,
                $request->validated()['last_delivered_message_id']
            );

            return $this->success(null, 'Messages marked as delivered.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 9c. Edit Message
     */
    public function editMessage(Request $request, UserChatMessage $message): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $editedMessage = $this->chatService->editMessage(Auth::user(), $message, $request->content);
            return $this->success(new MessageResource($editedMessage), 'Message edited.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    /**
     * 10. Delete Message
     */
    public function deleteMessage(UserChatMessage $message, Request $request): JsonResponse
    {
        $forEveryone = $request->boolean('delete_for_everyone');

        try {
            $this->chatService->deleteMessage(Auth::user(), $message, $forEveryone);
            return $this->success(null, 'Message deleted');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
}
