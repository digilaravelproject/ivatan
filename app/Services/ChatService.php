<?php

namespace App\Services;

use App\Events\Chat\MessageRead;
use App\Events\Chat\MessageSent;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * ✅ Mark Messages as Read
     */
    public function markAsRead(User $user, int $chatId, int $lastReadMessageId)
    {
        $participant = UserChatParticipant::where('chat_id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if ($participant && $lastReadMessageId > $participant->last_read_message_id) {
            $participant->update(['last_read_message_id' => $lastReadMessageId]);
            try {
                broadcast(new MessageRead($chatId, $user->id, $lastReadMessageId))->toOthers();
            } catch (\Exception $e) {
                \Log::error("Broadcast Error: " . $e->getMessage());
            }
        }
        return true;
    }

    /**
     * ✅ Get/Create Private Chat
     */
    public function getPrivateChat(int $myId, int $otherId)
    {
        $chat = UserChat::where('type', 'private')
            ->whereHas('participants', fn($q) => $q->where('user_id', $myId))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherId))
            ->first();

        if ($chat) return $chat;

        return DB::transaction(function () use ($myId, $otherId) {
            $chat = UserChat::create(['type' => 'private', 'last_message_at' => now()]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $myId]);
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $otherId]);
            return $chat;
        });
    }

    /**
     * ✅ Create Group Chat
     */
    public function createGroup(int $ownerId, string $name, array $participantIds, $file = null)
    {
        return DB::transaction(function () use ($ownerId, $name, $participantIds, $file) {
            $avatarPath = null;
            if ($file) {
                $avatarPath = $file->store('chat_avatars', 'public');
            }

            $chat = UserChat::create([
                'type' => 'group',
                'name' => $name,
                'owner_id' => $ownerId,
                'avatar_path' => $avatarPath,
                'last_message_at' => now(),
            ]);

            // Add Owner as Admin
            UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $ownerId, 'is_admin' => true]);

            // Add Members
            foreach (array_unique($participantIds) as $userId) {
                if ($userId != $ownerId) {
                    UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $userId]);
                }
            }

            $this->sendSystemMessage($chat->id, "Group created by " . Auth::user()->name);
            return $chat;
        });
    }

    /**
     * ✅ Add Participants (Logic: Check Admin)
     */
    public function addParticipants(User $user, UserChat $chat, array $memberIds)
    {
        // 1. Check if requester is Admin
        $isAdmin = $chat->participants()
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->exists();

        if (!$isAdmin) {
            throw new \Exception("Only admins can add members.");
        }

        DB::transaction(function () use ($chat, $memberIds, $user) {
            $addedNames = [];
            foreach ($memberIds as $id) {
                if (!$chat->participants()->where('user_id', $id)->exists()) {
                    UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $id]);
                    $newUser = User::find($id);
                    if ($newUser) $addedNames[] = $newUser->name;
                }
            }

            if (count($addedNames) > 0) {
                $this->sendSystemMessage($chat->id, $user->name . " added " . implode(", ", $addedNames));
            }
        });

        return true;
    }

    /**
     * ✅ Leave Group or Remove Member (Updated Logic)
     */
    public function leaveChat(User $requester, UserChat $chat, int $targetUserId)
    {
        $participant = $chat->participants()->where('user_id', $targetUserId)->first();

        if (!$participant) {
            throw new \Exception("User is not in this chat.");
        }

        // === CASE 1: Self Leave (Leaving the Group) ===
        if ($requester->id === $targetUserId) {

            // Check if user was an Admin
            $wasAdmin = $participant->is_admin;

            // Remove the user
            $participant->delete();
            $this->sendSystemMessage($chat->id, $requester->name . " left the group.");

            // ADMIN TRANSFER LOGIC
            // If the person leaving was an admin, assign a new admin randomly
            if ($wasAdmin) {
                // Get a random remaining participant
                $newAdmin = $chat->participants()->with('user')->inRandomOrder()->first();

                if ($newAdmin) {
                    $newAdmin->update(['is_admin' => true]);
                    $this->sendSystemMessage($chat->id, $newAdmin->user->name . " is now an Admin.");
                }
            }
        }
        // === CASE 2: Removing/Kicking Someone Else ===
        else {

            // Verify Requester is Admin
            $isRequesterAdmin = $chat->participants()
                ->where('user_id', $requester->id)
                ->where('is_admin', true)
                ->exists();

            if (!$isRequesterAdmin) {
                throw new \Exception("Only admins can remove participants.");
            }

            $targetUser = User::find($targetUserId);
            $participant->delete();

            $name = $targetUser ? $targetUser->name : 'a member';
            $this->sendSystemMessage($chat->id, $requester->name . " removed " . $name);
        }

        return true;
    }

    /**
     * ✅ Send Message
     */
    public function sendMessage(User $sender, UserChat $chat, array $data)
    {
        return DB::transaction(function () use ($sender, $chat, $data) {
            $attachmentPath = null;
            $meta = null;

            if (isset($data['attachment']) && $data['attachment']) {
                $file = $data['attachment'];
                $attachmentPath = $file->store("chat_attachments/{$chat->id}", 'public');
                $meta = [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getClientMimeType(),
                ];
            }

            $message = UserChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $sender->id,
                'content' => $data['content'] ?? null,
                'message_type' => $data['message_type'] ?? 'text',
                'attachment_path' => $attachmentPath,
                'meta' => $meta,
                'reply_to_message_id' => $data['reply_to_message_id'] ?? null,
                'delivered_at' => now(),
            ]);

            $chat->touch('last_message_at');

            broadcast(new MessageSent($message))->toOthers();
            $this->notifyParticipants($chat, $message, $sender);

            return $message;
        });
    }

    /**
     * ✅ Delete Message
     */
    public function deleteMessage(User $user, UserChatMessage $message, bool $deleteForEveryone = false)
    {
        if ($deleteForEveryone) {
            if ($message->sender_id !== $user->id) {
                throw new \Exception("Unauthorized to delete for everyone.");
            }
            $message->delete();
        } else {
            $hiddenUsers = $message->hidden_for_users ?? [];
            if (!in_array($user->id, $hiddenUsers)) {
                $hiddenUsers[] = $user->id;
                $message->hidden_for_users = $hiddenUsers;
                $message->save();
            }
        }
        return true;
    }

    public function sendSystemMessage(int $chatId, string $content)
    {
        $msg = UserChatMessage::create([
            'chat_id' => $chatId,
            'sender_id' => null,
            'content' => $content,
            'message_type' => 'system',
            'delivered_at' => now()
        ]);
        broadcast(new MessageSent($msg))->toOthers();
    }

    protected function notifyParticipants($chat, $message, $sender)
    {
        $recipients = $chat->participants()
            ->where('user_id', '!=', $sender->id)
            ->pluck('user_id');

        $users = User::whereIn('id', $recipients)->get();

        $this->notificationService->sendToUsers($users, 'chat_message', [
            'chat_id' => $chat->id,
            'sender_name' => $sender->name,
            'content' => $message->message_type === 'text' ? $message->content : 'Sent an attachment',
        ]);
    }
}
