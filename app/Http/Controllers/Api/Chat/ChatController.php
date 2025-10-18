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
use App\Jobs\Chat\ProcessMessageBroadcast;
use App\Jobs\Chat\ProcessMessageReadBroadcast;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // List user's chats
    public function index(Request $request)
    {
        $user = $request->user();
        $chats = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with(['lastMessage.sender', 'participants.user'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return response()->json($chats);
    }

    public function show($chatId, Request $request)
    {
        try {
            // Retrieve the chat with participants and messages
            $chat = UserChat::with(['participants.user', 'messages.sender'])
                ->findOrFail($chatId);

            // Check if the current user is a participant in the chat
            $user = $request->user();
            if (! $chat->participants->contains('user_id', $user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Map participants and include their last seen timestamp
            $participants = $chat->participants->map(function ($participant) {
                return [
                    'id' => $participant->user->id,
                    'name' => $participant->user->name,
                    'username' => $participant->user->username,
                    'avatar' => $participant->user->profile_photo_path ?? null,
                    'last_seen' => $participant->last_read_message ? $participant->last_read_message->created_at : null,
                ];
            });

            // Get the last message
            $lastMessage = $chat->messages->last();
            $lastMessageData = $lastMessage ? $lastMessage->only(['id', 'content', 'type', 'created_at']) : null;

            return response()->json([
                'id' => $chat->id,
                'type' => $chat->type,
                'title' => $chat->title,
                'participants_count' => $chat->participants->count(), // Return total participants count
                'participants' => $participants,
                'last_message' => $lastMessageData,
                'message_count' => $chat->messages->count(), // Return total message count in the chat
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle chat not found
            return response()->json(['error' => 'Chat not found'], 404);
        } catch (\Exception $e) {
            // General error handler for any other issues
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }


    // Open or create private chat between auth user and other_user_id
    public function openPrivate(CreatePrivateChatRequest $request)
    {
        try {
            $user = $request->user();
            $otherId = $request->other_user_id;

            // Find existing private chat that contains both participants (in any order)
            $chat = UserChat::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherId))
                ->first();

            // If chat doesn't exist, create a new private chat
            if (! $chat) {
                $chat = UserChat::create(['type' => 'private']);
                // Add participants to the new chat
                UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id, 'is_admin' => false]);
                UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $otherId, 'is_admin' => false]);
            }

            // Load the participants and last message for the chat
            $chat->load('participants.user', 'lastMessage');

            return response()->json(['success' => true, 'chat' => $chat]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Create group chat
    public function createGroup(CreateGroupChatRequest $request)
    {
        try {
            // Start by getting the authenticated user
            $user = $request->user();

            // Create the new group chat
            $chat = UserChat::create([
                'type' => 'group',
                'name' => $request->name,
                'owner_id' => $user->id,
            ]);

            // Add the owner as an admin
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id, 'is_admin' => true]);

            // Add members to the chat
            foreach ($request->member_ids as $memberId) {
                if ($memberId == $user->id) {
                    continue; // Skip if the member is the owner (user themselves)
                }
                UserChatParticipant::firstOrCreate([
                    'chat_id' => $chat->id,
                    'user_id' => $memberId,
                ]);
            }

            // Load the necessary fields for participants
            $chat->load(['participants.user:id,username,phone,profile_photo_path,name']);

            // Transform participants to only include the necessary data
            $participants = $chat->participants->map(function ($participant) {
                return [
                    'id' => $participant->user_id,
                    'name' => $participant->user->name,
                    'username' => $participant->user->username,
                    'phone' => $participant->user->phone,
                    'profile_photo_path' => $participant->user->profile_photo_path,
                ];
            });

            // Get the total count of participants
            $totalParticipants = $chat->participants->count();

            // Return the response with success status and chat details
            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'type' => $chat->type,
                    'name' => $chat->name,
                    'owner_id' => $chat->owner_id,
                    'total_participants' => $totalParticipants, // Add total participants field
                    'participants' => $participants,
                ],
            ], 201);
        } catch (\Exception $e) {
            // If an error occurs, catch the exception and return an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the group chat.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Add participants to group (owner or admin)
    public function addParticipants(AddParticipantsRequest $request, UserChat $chat)
    {
        try {
            $user = $request->user();

            // Check authorization via policy
            $this->authorize('addParticipant', $chat);

            // Check if the chat is a group
            if ($chat->type !== 'group') {
                return response()->json(['error' => 'Not a group chat'], 422);
            }

            // Add the new participants to the group
            $newParticipants = [];
            foreach ($request->member_ids as $memberId) {
                // Avoid adding the same user twice
                $participant = UserChatParticipant::firstOrCreate([
                    'chat_id' => $chat->id,
                    'user_id' => $memberId,
                ]);

                // Collect the added participants for response
                $newParticipants[] = $participant->user_id;
            }

            // Reload the participants with necessary user details
            $chat->load(['participants.user:id,username,phone,profile_photo_path,name']);

            // Transform participants to include only necessary details
            $participants = $chat->participants->map(function ($participant) {
                return [
                    'id' => $participant->user_id,
                    'name' => $participant->user->name,
                    'username' => $participant->user->username,
                    'phone' => $participant->user->phone,
                    'profile_photo_path' => $participant->user->profile_photo_path,
                ];
            });

            // Get the total count of participants
            $totalParticipants = $chat->participants->count();

            // Return response with success and updated chat details
            return response()->json([
                'success' => true,
                'message' => 'Participants added successfully', // Success message
                'new_participants' => $newParticipants, // Newly added participants
                'chat' => [
                    'id' => $chat->id,
                    'type' => $chat->type,
                    'name' => $chat->name,
                    'owner_id' => $chat->owner_id,
                    'total_participants' => $totalParticipants, // Add total participants field
                    'participants' => $participants,
                ],
            ]);
        } catch (\Exception $e) {
            // Catch any error and return a response with error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding participants.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Remove participant (only admin/owner)
    public function removeParticipant(UserChat $chat, $userId, Request $request)
    {
        $user = $request->user();

        $isAdmin = UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->where('is_admin', true)->exists()
            || $chat->owner_id === $user->id;

        if (! $isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // cannot remove owner
        if ((int) $userId === (int) $chat->owner_id) {
            return response()->json(['error' => 'Cannot remove owner'], 422);
        }

        UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $userId)->delete();

        $chat->load('participants.user');

        return response()->json(['success' => true, 'chat' => $chat]);
    }

    /**
     * Leave the group or remove a participant (admin only)
     */
    public function leaveOrRemove(UserChat $chat, Request $request)
    {
        $user = $request->user();
        $actionData = $request->input('action'); // "leave" or "remove"
        $memberIds = $request->input('member_ids'); // Array of member IDs for removal (only for owner)

        // Validate the action
        if (!$actionData || !in_array($actionData, ['leave', 'remove'])) {
            return response()->json(['error' => 'Invalid action provided'], 422);
        }

        if ($actionData === 'leave') {
            // If the user is the owner, prevent leaving
            if ($chat->owner_id === $user->id) {
                return response()->json(['error' => 'Owner cannot leave the group. Transfer ownership first.'], 422);
            }

            // Participants can leave their own group
            $this->authorize('leaveGroup', $chat);

            // Remove the participant from the group
            UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->delete();

            // Return success with message for leaving user
            return response()->json([
                'success' => true,
                'message' => 'You no longer exist in this group.',
            ]);
        } elseif ($actionData === 'remove') {
            // Only the owner or an admin can remove participants
            $this->authorize('removeParticipant', $chat);

            // Ensure that member_ids is provided for removal
            if (!$memberIds || !is_array($memberIds)) {
                return response()->json(['error' => 'member_ids array is required'], 422);
            }

            // Only owner can remove multiple participants
            if ($chat->owner_id !== $user->id) {
                return response()->json(['error' => 'Only the owner can remove participants'], 422);
            }

            // Validate each member_id
            $participants = UserChatParticipant::whereIn('user_id', $memberIds)->where('chat_id', $chat->id)->get();

            if ($participants->count() !== count($memberIds)) {
                return response()->json(['error' => 'Some participants not found in the chat'], 404);
            }

            // Remove the participants
            UserChatParticipant::whereIn('user_id', $memberIds)->where('chat_id', $chat->id)->delete();

            // Return success with message for admin removing participants
            return response()->json([
                'success' => true,
                'message' => 'Participants removed successfully.',
                'removed_user_ids' => $memberIds,
                'remaining_users' => $chat->participants()->pluck('user_id')->toArray(),
                "Chat Admin" => $chat->owner_id
            ]);
        }
    }


    // Leave group (participant)
    public function leave(UserChat $chat, Request $request)
    {
        $user = $request->user();

        UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->delete();

        // if leaving owner, optional: transfer ownership or delete group. (we keep simple: owner cannot leave)
        if ($chat->owner_id === $user->id) {
            return response()->json(['error' => 'Owner cannot leave the group. Transfer ownership first.'], 422);
        }

        return response()->json(['success' => true]);
    }

    // Send message (text / attachment)
    public function sendMessage(SendMessageRequest $request, UserChat $chat): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the chat and verify if it exists
            // $chat = UserChat::findOrFail($chatId);

            // Verify if the user is a participant of the chat
            $isParticipant = UserChatParticipant::where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->exists();

            if (! $isParticipant) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Prepare for message creation
            $messageType = $request->message_type ?? 'text';
            $attachmentPath = null;
            $meta = null;

            // Handle attachment if exists
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $ext = $file->getClientOriginalExtension();
                $name = Str::random(40) . '.' . $ext;
                $path = $file->storeAs("chat_attachments/{$chat->id}", $name, 'public');
                $attachmentPath = $path;
                $meta = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getClientMimeType(),
                ];
            }

            // Create the message
            $message = UserChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'content' => $request->content ?? null,
                'message_type' => $messageType,
                'attachment_path' => $attachmentPath,
                'meta' => $meta,
                'reply_to_message_id' => $request->reply_to_message_id ?? null,
            ]);

            // Update last message timestamp in the chat
            $chat->last_message_at = now();
            $chat->save();

            // Send notifications to participants
            $this->sendNotifications($chat, $message, $user);

            // Broadcast the message to others
            ProcessMessageBroadcast::dispatch($message);

            // Return the response with the created message
            return response()->json(['success' => true, 'message' => $message], 201);
        } catch (\Exception $e) {
            // Detailed error logging for unexpected errors
            \Log::error('Message sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

    protected function sendNotifications($chat, $message, $user)
    {
        $receivers = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id');

        foreach ($receivers as $receiverId) {
            $receiver = User::find($receiverId);
            if (! $receiver) {
                continue;
            }

            try {
                $this->notificationService->sendToUser(
                    $receiver,
                    'chat_message',
                    [
                        'chat_id' => $chat->id,
                        'message_id' => $message->id,
                        'sender_id' => $user->id,
                        'sender_name' => $user->name,
                        'content' => $message->content,
                        'type' => $message->message_type,
                    ]
                );
            } catch (\Throwable $e) {
                \Log::error("Notification failed for user {$receiverId}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Notify the sender (optional)
        $this->notificationService->sendToUser(
            $user,
            'chat_message',
            [
                'chat_id' => $chat->id,
                'message_id' => $message->id,
                'content' => $message->content,
            ]
        );
    }

    public function sendMessage_old(SendMessageRequest $request)
    {
        $user = $request->user();
        $chat = UserChat::findOrFail($request->chat_id);

        // verify participant
        $isParticipant = UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->exists();
        if (! $isParticipant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messageType = $request->message_type ?? 'text';
        $attachmentPath = null;
        $meta = null;

        if ($request->hasFile('attachment')) {
            // store file
            $file = $request->file('attachment');
            $ext = $file->getClientOriginalExtension();
            $name = Str::random(40) . '.' . $ext;
            $path = $file->storeAs("chat_attachments/{$chat->id}", $name, 'public');
            $attachmentPath = $path;
            $meta = [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getClientMimeType(),
            ];
        }

        $message = UserChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'content' => $request->content ?? null,
            'message_type' => $messageType,
            'attachment_path' => $attachmentPath,
            'meta' => $meta,
            'reply_to_message_id' => $request->reply_to_message_id ?? null,
        ]);

        // update chat last message time
        $chat->last_message_at = now();
        $chat->save();

        // notification bhejna
        // send notification first
        $receivers = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id');

        foreach ($receivers as $receiverId) {
            $receiver = User::find($receiverId);
            if (! $receiver) {
                continue;
            }

            try {
                $this->notificationService->sendToUser(
                    $receiver,
                    'chat_message',
                    [
                        'chat_id' => $chat->id,
                        'message_id' => $message->id,
                        'sender_id' => $user->id,
                        'sender_name' => $user->name,
                        'content' => $message->content,
                        'type' => $messageType,
                    ]
                );
            } catch (\Throwable $e) {
                \Log::error("Notification failed for user {$receiverId}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // broadcast
        // broadcast(new MessageSent($message))->toOthers();
        ProcessMessageBroadcast::dispatch($message);

        $this->notificationService->sendToUser(
            $user,  // jise message mila
            'chat_message',
            ['chat_id' => $chat->id, 'message_id' => $message->id, 'content' => $message->content]
        );

        return response()->json(['success' => true, 'message' => $message], 201);
    }

    // Fetch messages (pagination)
    /**
     * Fetch messages for a specific chat.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function messages(UserChat $chat, Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        // Check if the user is a participant of this chat
        $isParticipant = $chat->participants()->where('user_id', $user->id)->exists();
        if (!$isParticipant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the participant to fetch the last read message ID
        $participant = $chat->participants()->where('user_id', $user->id)->first();

        // Default to 30 messages per page, but allow overriding via query string
        $perPage = (int) $request->query('per_page', 30);

        // Get the messages with pagination (Cursor pagination for efficiency)
        $messages = $chat->messages()
            ->with(['sender' => function ($query) {
                $query->select('id', 'username', 'name', 'profile_photo_path'); // Only load necessary columns from the sender
            }])
            ->latest('created_at') // Order messages by latest created_at first
            ->cursorPaginate($perPage);

        // Add `seen_at` timestamp to each message if it's been marked as read
        $messages->getCollection()->transform(function ($message) use ($participant) {
            // If the message is the last read message, set the seen_at timestamp
            if ($participant && $message->id === $participant->last_read_message_id) {
                // Attach the timestamp when the message was marked as seen
                $message->seen_at = $participant->updated_at;
            } else {
                // If it's not the last read message, no need to set seen_at
                $message->seen_at = null;
            }

            // If the attachment_path is not a part of the message by default, add it here
            $message->attachment_path = $message->attachment_path ?? null;

            return $message;
        });

        // Return paginated messages as JSON
        return response()->json($messages);
    }


    public function messages_old(UserChat $chat, Request $request)
    {
        $user = $request->user();

        $isParticipant = UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->exists();
        if (! $isParticipant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $perPage = (int) $request->query('per_page', 30);
        $messages = $chat->messages()->with('sender')->paginate($perPage);

        return response()->json($messages);
    }

    // Mark read
    public function markRead(MarkReadRequest $request, UserChat $chat): JsonResponse
    {
        $user = $request->user();
        $lastId = $request->last_read_message_id;

        // Check if the user is a participant of the chat
        $participant = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate that the provided last_read_message_id is part of the chat's messages
        $message = $chat->messages()->find($lastId);

        if (!$message) {
            return response()->json(['error' => 'Invalid message ID'], 400); // Message not found
        }

        // Ensure the message was sent by someone else (not the user themselves)
        if ($message->sender_id === $user->id) {
            return response()->json(['error' => 'You cannot mark your own message as read'], 400);
        }

        // Check if the user has already marked this message as read
        if ($participant->last_read_message_id === $lastId) {
            return response()->json(['message' => 'This message has already been marked as read'], 200);
        }

        // Update the participant's last read message ID and timestamp
        $participant->last_read_message_id = $lastId;
        $participant->save();

        // Broadcast the read event (uncomment if needed)
        // broadcast(new MessageRead($chat->id, $user->id, $lastId))->toOthers();
        ProcessMessageReadBroadcast::dispatch($chat->id, $user->id, $lastId);

        // Return the marked message along with the success response
        return response()->json([
            'success' => true,
            'message' => 'Message marked as read successfully.',
            'last_read_message' => $message // Include the message that was marked as read
        ]);
    }
}
