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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ChatController extends Controller
{
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
        $chat = UserChat::with(['participants.user', 'messages.sender'])
            ->findOrFail($chatId);

        // Check if current user is part of chat
        if (!$chat->participants->contains('user_id', $request->user()->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $chat->id,
            'type' => $chat->type,
            'title' => $chat->title,
            'participants' => $chat->participants->map(function ($p) {
                return [
                    'id' => $p->user->id,
                    'name' => $p->user->name,
                    'avatar' => $p->user->avatar ?? null,
                ];
            }),
            'last_message' => $chat->messages->last()?->only(['id', 'content', 'type', 'created_at']),
        ]);
    }

    // Open or create private chat between auth user and other_user_id
    public function openPrivate(CreatePrivateChatRequest $request)
    {
        $user = $request->user();
        $otherId = $request->other_user_id;

        // Find existing private chat which contains both participants (and type private)
        $chat = UserChat::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherId))
            ->first();

        if (! $chat) {
            $chat = UserChat::create(['type' => 'private']);
            // add participants
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id, 'is_admin' => false]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $otherId, 'is_admin' => false]);
        }

        $chat->load('participants.user', 'lastMessage');

        return response()->json(['success' => true, 'chat' => $chat]);
    }

    // Create group chat
    public function createGroup(CreateGroupChatRequest $request)
    {
        $user = $request->user();

        $chat = UserChat::create([
            'type' => 'group',
            'name' => $request->name,
            'owner_id' => $user->id,
        ]);

        // add owner as admin
        UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $user->id, 'is_admin' => true]);

        // add members
        foreach ($request->member_ids as $memberId) {
            if ($memberId == $user->id) continue;
            UserChatParticipant::firstOrCreate([
                'chat_id' => $chat->id,
                'user_id' => $memberId
            ]);
        }

        $chat->load('participants.user');

        return response()->json(['success' => true, 'chat' => $chat], 201);
    }

    // Add participants to group (owner or admin)
    public function addParticipants(AddParticipantsRequest $request, UserChat $chat)
    {
        $user = $request->user();

        // only group & only owner/admin
        if ($chat->type !== 'group') {
            return response()->json(['error' => 'Not a group chat'], 422);
        }

        $isAdmin = UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->where('is_admin', true)->exists()
            || $chat->owner_id === $user->id;

        if (! $isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        foreach ($request->member_ids as $memberId) {
            UserChatParticipant::firstOrCreate([
                'chat_id' => $chat->id,
                'user_id' => $memberId
            ]);
        }

        $chat->load('participants.user');

        return response()->json(['success' => true, 'chat' => $chat]);
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
        if ((int)$userId === (int)$chat->owner_id) {
            return response()->json(['error' => 'Cannot remove owner'], 422);
        }

        UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $userId)->delete();

        $chat->load('participants.user');

        return response()->json(['success' => true, 'chat' => $chat]);
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
    public function sendMessage(SendMessageRequest $request)
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

        // broadcast
        // broadcast(new MessageSent($message))->toOthers();
        ProcessMessageBroadcast::dispatch($message);

        return response()->json(['success' => true, 'message' => $message], 201);
    }

    // Fetch messages (pagination)
    public function messages(UserChat $chat, Request $request)
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
    public function markRead(MarkReadRequest $request)
    {
        $user = $request->user();
        $chat = UserChat::findOrFail($request->chat_id);
        $lastId = $request->last_read_message_id;

        $participant = UserChatParticipant::where('chat_id', $chat->id)->where('user_id', $user->id)->first();
        if (! $participant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $participant->last_read_message_id = $lastId;
        $participant->save();

        // broadcast read event
        // broadcast(new MessageRead($chat->id, $user->id, $lastId))->toOthers();
        ProcessMessageReadBroadcast::dispatch($chat->id, $user->id, $lastId);

        return response()->json(['success' => true]);
    }
}
