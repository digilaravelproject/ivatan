<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\AddParticipantsRequest;
use App\Http\Requests\Chat\CreateGroupChatRequest;
use App\Http\Requests\Chat\CreatePrivateChatRequest;
use App\Http\Requests\Chat\MarkReadRequest;
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
     * 1. Inbox & Listing
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $filter = $request->query('filter');

        $query = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with(['participants.user', 'lastMessage.sender'])
            ->withCount(['participants']);

        if ($filter === 'groups') {
            $query->where('type', 'group');
        }

        $chats = $query->orderByDesc('last_message_at')->paginate(20);

        return $this->success([
            'chats' => ChatResource::collection($chats)->response()->getData(true)
        ]);
    }

    /**
     * 2. Chat Details
     */
    public function show(UserChat $chat): JsonResponse
    {
        return $this->success(new ChatResource($chat->load('participants.user')));
    }

    /**
     * 3. Fetch Messages
     */
    public function messages(UserChat $chat, Request $request): JsonResponse
    {
        $messages = $chat->messages()
            ->visibleToUser(Auth::id())
            ->with(['sender', 'replyTo.sender'])
            ->latest()
            ->cursorPaginate(30);

        return $this->success(MessageResource::collection($messages));
    }

    /**
     * 4. Open/Start Private Chat
     */
    public function openPrivate(CreatePrivateChatRequest $request): JsonResponse
    {
        $chat = $this->chatService->getPrivateChat(Auth::id(), $request->other_user_id);
        return $this->success(new ChatResource($chat));
    }

    /**
     * 5. Create Group
     */
    public function createGroup(CreateGroupChatRequest $request): JsonResponse
    {
        $chat = $this->chatService->createGroup(
            Auth::id(),
            $request->name,
            $request->participant_ids,
            $request->file('avatar')
        );
        return $this->success(new ChatResource($chat), 'Group created', 201);
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
        $message = $this->chatService->sendMessage(Auth::user(), $chat, $request->validated());
        return $this->success(new MessageResource($message), 'Sent', 201);
    }

    /**
     * 9. Mark as Read
     */
    public function markRead(MarkReadRequest $request, UserChat $chat): JsonResponse
    {
        $this->chatService->markAsRead(
            Auth::user(),
            $chat->id,
            $request->validated()['last_read_message_id']
        );

        return $this->success(null, 'Messages marked as read.');
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
