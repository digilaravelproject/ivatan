<?php

namespace App\Services;

use App\Events\Chat\MessageRead;
use App\Events\Chat\MessageSent;
use App\Events\Chat\MessageDelivered;
use App\Events\Chat\MessageDeleted;
use App\Events\Chat\MessageEdited;
use App\Events\Chat\GroupCreated;
use App\Events\Chat\ParticipantAdded;
use App\Events\Chat\ParticipantRemoved;
use App\Events\Chat\ParticipantLeft;
use App\Events\Chat\AdminStatusChanged;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\User;
use App\Models\UserBlock;
use App\Services\LiveChatGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ChatService
{
    protected NotificationService $notificationService;
    protected LiveChatGroupService $liveChatGroupService;

    public function __construct(
        NotificationService $notificationService,
        LiveChatGroupService $liveChatGroupService
    ) {
        $this->notificationService = $notificationService;
        $this->liveChatGroupService = $liveChatGroupService;
    }

    /**
     * ✅ Validate Messaging Privacy Settings, Blocks, and Private Accounts
     */
    public function validateMessagingPrivacy(User $sender, User $receiver): void
    {
        // 1. Check Bidirectional Block List
        $hasBlock = UserBlock::where(function ($q) use ($sender, $receiver) {
            $q->where('user_id', $sender->id)->where('blocked_user_id', $receiver->id);
        })->orWhere(function ($q) use ($sender, $receiver) {
            $q->where('user_id', $receiver->id)->where('blocked_user_id', $sender->id);
        })->exists();

        if ($hasBlock) {
            throw new \Exception('Messaging blocked.');
        }

        // 2. Check Messaging Privacy Rules
        $messagingPrivacy = $receiver->messaging_privacy ?? 'everyone';

        if ($messagingPrivacy === 'none') {
            throw new \Exception('This user does not accept direct messages.');
        }

        // 3. Private Account / Followers check
        $isFollowing = $sender->isFollowing($receiver);
        
        if ($messagingPrivacy === 'followers' && !$isFollowing) {
            throw new \Exception('Only followers can message this user.');
        }

        if ($receiver->account_privacy === 'private' && !$isFollowing) {
            throw new \Exception('This account is private. You must follow them to send a message.');
        }

        // 4. Recruiter / Business Profile Messaging Limit Check
        $isRecruiter = $receiver->is_employer || $receiver->is_seller;
        if ($isRecruiter) {
            $dmLimit = $sender->getFeatureLimit('dm_recruiters_msme');
            if (strtolower($dmLimit) === 'no' || !$dmLimit) {
                throw new \Exception('Your current subscription plan does not allow messaging recruiter or business accounts.');
            }
        }
    }

    /**
     * ✅ Mark Messages as Read
     */
    public function markAsRead(User $user, int $chatId, int $lastReadMessageId)
    {
        $participant = UserChatParticipant::where('chat_id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if ($participant && ($participant->last_read_message_id === null || $lastReadMessageId > $participant->last_read_message_id)) {
            $participant->update(['last_read_message_id' => $lastReadMessageId]);
            try {
                $chat = $participant->chat;
                $targetUserId = null;
                if ($chat && $chat->type === 'private') {
                    $otherParticipant = $chat->participants()->where('user_id', '!=', $user->id)->first();
                    if ($otherParticipant) {
                        $targetUserId = $otherParticipant->user_id;
                    }
                }
                broadcast(new MessageRead($chatId, $user->id, $lastReadMessageId, $targetUserId))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcast Error: MessageRead failed', [
                    'error' => $e->getMessage(),
                    'chat_id' => $chatId,
                    'target_user_id' => $targetUserId,
                ]);
            }
        }
        return true;
    }

    /**
     * ✅ Mark Messages as Delivered
     */
    public function markAsDelivered(User $user, int $chatId, int $lastDeliveredMessageId)
    {
        $participant = UserChatParticipant::where('chat_id', $chatId)
            ->where('user_id', $user->id)
            ->first();

        if ($participant && ($participant->last_delivered_message_id === null || $lastDeliveredMessageId > $participant->last_delivered_message_id)) {
            $participant->update(['last_delivered_message_id' => $lastDeliveredMessageId]);
            try {
                $chat = $participant->chat;
                if ($chat && $chat->type === 'private') {
                    $otherParticipant = $chat->participants()->where('user_id', '!=', $user->id)->first();
                    if ($otherParticipant) {
                        broadcast(new MessageDelivered($chatId, $lastDeliveredMessageId, $user->id, $otherParticipant->user_id))->toOthers();
                    }
                }
            } catch (\Exception $e) {
                Log::error('Broadcast Error: MessageDelivered failed', [
                    'error' => $e->getMessage(),
                    'chat_id' => $chatId,
                    'target_user_id' => $otherParticipant->user_id ?? null,
                ]);
            }
        }
        return true;
    }

    /**
     * ✅ Get/Create Private Chat
     * Includes block check: prevents chat between blocked users.
     */
    public function getPrivateChat(int $myId, int $otherId)
    {
        $me = User::findOrFail($myId);
        $other = User::findOrFail($otherId);

        $this->validateMessagingPrivacy($me, $other);

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

            // Broadcast GroupCreated to all participants
            $participants = $chat->participants;
            foreach ($participants as $p) {
                try {
                    broadcast(new GroupCreated(
                        $chat->id,
                        $chat->name,
                        $chat->avatar_url,
                        $ownerId,
                        $p->user_id
                    ))->toOthers();
                } catch (\Exception $e) {
                    Log::error('Broadcast Error: GroupCreated failed', [
                        'error' => $e->getMessage(),
                        'chat_id' => $chat->id,
                        'target_user_id' => $p->user_id ?? null,
                    ]);
                }
            }

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

        DB::transaction(function() use ($chat, $memberIds, $user) {
            $addedNames = [];
            foreach ($memberIds as $id) {
                if (!$chat->participants()->where('user_id', $id)->exists()) {
                    UserChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $id]);
                    $newUser = User::find($id);
                    if ($newUser) {
                        $addedNames[] = $newUser->name;

                        // Broadcast ParticipantAdded event to the channel and specifically to the new member
                        try {
                            broadcast(new ParticipantAdded(
                                $chat->id,
                                ['id' => $newUser->id, 'name' => $newUser->name],
                                $user->id
                            ))->toOthers();
                        } catch (\Exception $e) {
                            Log::error('Broadcast Error: ParticipantAdded failed', [
                                'error' => $e->getMessage(),
                                'chat_id' => $chat->id,
                                'new_user_id' => $newUser->id ?? null,
                            ]);
                        }
                    }
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

            // Broadcast ParticipantLeft
            try {
                broadcast(new ParticipantLeft($chat->id, $requester->id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcast Error: ParticipantLeft failed', [
                    'error' => $e->getMessage(),
                    'chat_id' => $chat->id,
                ]);
            }

            // ADMIN TRANSFER LOGIC
            // If the person leaving was an admin, assign a new admin randomly
            if ($wasAdmin) {
                // Get a random remaining participant
                $newAdmin = $chat->participants()->with('user')->inRandomOrder()->first();
                
                if ($newAdmin) {
                    $newAdmin->update(['is_admin' => true]);
                    $this->sendSystemMessage($chat->id, $newAdmin->user->name . " is now an Admin.");

                    // Broadcast AdminStatusChanged
                    try {
                        broadcast(new AdminStatusChanged($chat->id, $newAdmin->user_id, true, $requester->id))->toOthers();
                    } catch (\Exception $e) {
                        Log::error('Broadcast Error: AdminStatusChanged failed', [
                            'error' => $e->getMessage(),
                            'chat_id' => $chat->id,
                            'new_admin_id' => $newAdmin->user_id ?? null,
                        ]);
                    }
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

            // Broadcast ParticipantRemoved
            try {
                broadcast(new ParticipantRemoved($chat->id, $targetUserId, $requester->id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Broadcast Error: ParticipantRemoved failed', [
                    'error' => $e->getMessage(),
                    'chat_id' => $chat->id,
                    'target_user_id' => $targetUserId,
                ]);
            }
        }

        return true;
    }

    /**
     * ✅ Send Message
     * Includes block check for private chats.
     */
    public function sendMessage(User $sender, UserChat $chat, array $data)
    {
        // === LIVE GROUP CHECKS (ban, mute, chat_mode) ===
        if ($chat->live_chat_group_id) {
            $this->liveChatGroupService->checkCanSend($sender, $chat);
        }

        // === PRIVACY & BLOCK CHECK for private chats ===
        if ($chat->type === 'private') {
            $otherParticipant = $chat->participants()
                ->where('user_id', '!=', $sender->id)
                ->first();

            if ($otherParticipant) {
                $this->validateMessagingPrivacy($sender, $otherParticipant->user);
            }
        }

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
                'delivered_at' => null,
            ]);

            // Update sender's last_read_message_id
            UserChatParticipant::where('chat_id', $chat->id)
                ->where('user_id', $sender->id)
                ->update(['last_read_message_id' => $message->id]);

            $chat->touch('last_message_at');

            broadcast(new MessageSent($message))->toOthers();
            $this->notifyParticipants($chat, $message, $sender);

            return $message;
        });
    }

    /**
     * ✅ Edit Message
     */
    public function editMessage(User $user, UserChatMessage $message, string $newContent)
    {
        if ($message->sender_id !== $user->id) {
            throw new \Exception("Unauthorized to edit this message.");
        }

        $message->update([
            'content' => $newContent,
        ]);

        broadcast(new MessageEdited($message->chat_id, $message->id, $newContent, now()->toISOString()))->toOthers();

        return $message;
    }

    /**
     * ✅ Delete Message
     */
    public function deleteMessage(User $user, UserChatMessage $message, bool $deleteForEveryone = false)
    {
        $chatId = $message->chat_id;
        $messageId = $message->id;

        if ($deleteForEveryone) {
            if ($message->sender_id !== $user->id) {
                throw new \Exception("Unauthorized to delete for everyone.");
            }
            $message->delete();
            
            // Broadcast deletion to everyone
            broadcast(new MessageDeleted($chatId, $messageId, 'everyone', $user->id))->toOthers();
        } else {
            $hiddenUsers = $message->hidden_for_users ?? [];
            if (!in_array($user->id, $hiddenUsers)) {
                $hiddenUsers[] = $user->id;
                $message->hidden_for_users = $hiddenUsers;
                $message->save();
            }
            
            // Broadcast deletion to self
            broadcast(new MessageDeleted($chatId, $messageId, 'me', $user->id))->toOthers();
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
    /**
     * Notify participants of a chat
     * @param UserChat $chat
     * @param UserChatMessage $message
     * @param User $sender
     */
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

    /**
     * Get and filter user chats (inbox)
     */
    public function getUserChats(User $user, ?string $filter)
    {
        $query = UserChat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->withoutBlocked($user)
            ->select('user_chats.*')
            ->selectSub(function ($q) use ($user) {
                $q->from('user_chat_messages')
                    ->whereColumn('user_chat_messages.chat_id', 'user_chats.id')
                    ->where('user_chat_messages.sender_id', '!=', $user->id)
                    ->where('user_chat_messages.id', '>', function ($sub) use ($user) {
                        $sub->from('user_chat_participants')
                            ->selectRaw('COALESCE(last_read_message_id, 0)')
                            ->whereColumn('user_chat_participants.chat_id', 'user_chats.id')
                            ->where('user_chat_participants.user_id', $user->id)
                            ->limit(1);
                    })
                    ->selectRaw('count(*)');
            }, 'unread_count')
            ->with(['participants.user', 'lastMessage.sender'])
            ->withCount(['participants']);

        if ($filter === 'live_groups') {
            $query->whereNotNull('live_chat_group_id');
        } else {
            $query->whereNull('live_chat_group_id');
        }

        if ($filter === 'groups') {
            $query->where('type', 'group');
        } elseif ($filter === 'business') {
            $query->whereHas('participants.user', function ($q) use ($user) {
                $q->where('is_seller', true)
                  ->where('id', '!=', $user->id);
            });
        } elseif ($filter === 'unread') {
            $query->whereHas('lastMessage', function ($q) use ($user) {
                $q->whereRaw('user_chat_messages.id > (
                    SELECT COALESCE(last_read_message_id, 0)
                    FROM user_chat_participants 
                    WHERE user_chat_participants.chat_id = user_chat_messages.chat_id 
                    AND user_chat_participants.user_id = ?
                )', [$user->id]);
            });
        } elseif ($filter === 'read') {
            $query->where(function ($mainQ) use ($user) {
                $mainQ->whereHas('lastMessage', function ($q) use ($user) {
                    $q->whereRaw('user_chat_messages.id <= (
                        SELECT COALESCE(last_read_message_id, 0)
                        FROM user_chat_participants 
                        WHERE user_chat_participants.chat_id = user_chat_messages.chat_id 
                        AND user_chat_participants.user_id = ?
                    )', [$user->id]);
                })
                ->orWhereDoesntHave('lastMessage'); 
            });
        }

        return $query->orderByDesc('last_message_at')->simplePaginate(20);
    }

    /**
     * Get chat messages and generate unread/user metadata
     */
    public function getChatMessages(User $user, UserChat $chat, $request)
    {
        $query = $chat->messages()
            ->visibleToUser($user->id)
            ->with(['sender', 'replyTo.sender']);

        if ($request->has('after_id')) {
            $query->where('id', '>', $request->input('after_id'));
            $messages = $query->oldest()->get(); 
        } else {
            $messages = $query->latest()->cursorPaginate(30); 
        }

        // Automatically mark the latest message as read when fetching messages
        $latestMessage = $chat->messages()->latest('id')->first();
        if ($latestMessage) {
            $this->markAsRead($user, $chat->id, $latestMessage->id);
        }

        $chat->loadMissing('participants.user');

        $unreadCount = 0;
        $participant = $chat->participants->firstWhere('user_id', $user->id);
        if ($participant) {
            $unreadCount = $chat->messages()
                ->where('id', '>', $participant->last_read_message_id ?? 0)
                ->where('sender_id', '!=', $user->id)
                ->count();
        }

        $otherUser = null;
        if ($chat->type === 'private') {
            $otherParticipant = $chat->participants->firstWhere('user_id', '!=', $user->id);
            if ($otherParticipant && $otherParticipant->user) {
                $otherUser = [
                    'id' => $otherParticipant->user->id,
                    'name' => $otherParticipant->user->name,
                    'avatar' => $otherParticipant->user->profile_photo_url,
                    'is_online' => (bool)$otherParticipant->user->is_online,
                ];
            }
        }

        return [
            'messages' => $messages,
            'meta' => [
                'unread_count' => (int)$unreadCount,
                'other_user' => $otherUser,
            ]
        ];
    }

    /**
     * Retrieve read receipts for a message
     */
    public function getMessageReadReceipts(User $user, UserChatMessage $message)
    {
        $chat = $message->chat;

        // Verify the user is a participant of this chat
        $isParticipant = $chat->participants()->where('user_id', $user->id)->exists();
        if (!$isParticipant) {
            throw new \Exception('Unauthorized.', 403);
        }

        // Find all participants who have read up to or past this message ID (excluding sender of this message)
        $readers = User::whereIn('id', function ($q) use ($message) {
            $q->select('user_id')
                ->from('user_chat_participants')
                ->where('chat_id', $message->chat_id)
                ->where('last_read_message_id', '>=', $message->id)
                ->where('user_id', '!=', $message->sender_id);
        })->get(['id', 'name', 'profile_photo_path']);

        return $readers->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->profile_photo_url,
            ];
        });
    }
}