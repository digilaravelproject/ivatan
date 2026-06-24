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
            $chats = $this->chatService->getUserChats(Auth::user(), $request->query('filter'));

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

    public function messages(UserChat $chat, Request $request): JsonResponse
    {
        try {
            $result = $this->chatService->getChatMessages(Auth::user(), $chat, $request);

            return $this->success([
                'messages' => MessageResource::collection($result['messages'])->response()->getData(true),
                'meta' => $result['meta']
            ]);
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

    /**
     * 11. Message Read Receipts
     */
    public function messageReadReceipts(UserChatMessage $message): JsonResponse
    {
        try {
            $readers = $this->chatService->getMessageReadReceipts(Auth::user(), $message);

            return $this->success([
                'readers' => $readers,
            ], 'Read receipts retrieved.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 403);
        }
    }
}
