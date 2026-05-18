<?php

namespace App\Services;

use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\LiveChatGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LiveChatGroupService
{
    public function create(array $data, User $admin): LiveChatGroup
    {
        return DB::transaction(function () use ($data, $admin) {
            $chat = UserChat::create([
                'type' => 'group',
                'name' => $data['name'],
                'owner_id' => $admin->id,
                'chat_mode' => $data['chat_mode'] ?? 'everyone',
                'last_message_at' => now(),
            ]);

            $group = LiveChatGroup::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(4),
                'description' => $data['description'] ?? null,
                'chat_mode' => $data['chat_mode'] ?? 'everyone',
                'is_active' => true,
                'created_by' => $admin->id,
            ]);

            $chat->live_chat_group_id = $group->id;
            $chat->save();

            UserChatParticipant::create([
                'chat_id' => $chat->id,
                'user_id' => $admin->id,
                'is_admin' => true,
            ]);

            $this->sendSystemMessage($chat->id, "Live chat group '{$group->name}' created.");

            return $group->fresh();
        });
    }

    public function update(LiveChatGroup $group, array $data): LiveChatGroup
    {
        DB::transaction(function () use ($group, $data) {
            $group->update([
                'name' => $data['name'] ?? $group->name,
                'description' => $data['description'] ?? $group->description,
                'chat_mode' => $data['chat_mode'] ?? $group->chat_mode,
                'is_active' => $data['is_active'] ?? $group->is_active,
            ]);

            if ($group->chat) {
                $group->chat->update([
                    'name' => $data['name'] ?? $group->chat->name,
                    'chat_mode' => $data['chat_mode'] ?? $group->chat->chat_mode,
                ]);
            }
        });

        return $group->fresh();
    }

    public function addUserToGroups(User $user): void
    {
        $groups = LiveChatGroup::active()->get();

        foreach ($groups as $group) {
            if (!$group->chat) continue;

            $exists = UserChatParticipant::where('chat_id', $group->chat->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                UserChatParticipant::create([
                    'chat_id' => $group->chat->id,
                    'user_id' => $user->id,
                    'is_admin' => false,
                ]);
            }
        }
    }

    public function addAllExistingUsers(): int
    {
        $groups = LiveChatGroup::active()->get();
        if ($groups->isEmpty()) return 0;

        $added = 0;
        User::chunk(200, function ($users) use ($groups, &$added) {
            foreach ($groups as $group) {
                if (!$group->chat) continue;
                foreach ($users as $user) {
                    $exists = UserChatParticipant::where('chat_id', $group->chat->id)
                        ->where('user_id', $user->id)
                        ->exists();
                    if (!$exists) {
                        UserChatParticipant::create([
                            'chat_id' => $group->chat->id,
                            'user_id' => $user->id,
                            'is_admin' => false,
                        ]);
                        $added++;
                    }
                }
            }
        });

        return $added;
    }

    public function removeParticipant(LiveChatGroup $group, int $userId): void
    {
        if (!$group->chat) throw new \Exception('Live chat group has no associated chat.');

        $participant = UserChatParticipant::where('chat_id', $group->chat->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) throw new \Exception('User is not a participant.');

        $user = User::find($userId);
        $participant->delete();
        $this->sendSystemMessage($group->chat->id, ($user->name ?? 'User') . ' was removed from the group.');
    }

    public function banParticipant(LiveChatGroup $group, int $userId): void
    {
        if (!$group->chat) throw new \Exception('Live chat group has no associated chat.');

        $participant = UserChatParticipant::where('chat_id', $group->chat->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) throw new \Exception('User is not a participant.');

        $participant->update([
            'is_banned' => true,
            'banned_at' => now(),
        ]);

        $user = User::find($userId);
        $this->sendSystemMessage($group->chat->id, ($user->name ?? 'User') . ' was banned from the group.');
    }

    public function unbanParticipant(LiveChatGroup $group, int $userId): void
    {
        if (!$group->chat) throw new \Exception('Live chat group has no associated chat.');

        $participant = UserChatParticipant::where('chat_id', $group->chat->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) throw new \Exception('User is not a participant.');

        $participant->update([
            'is_banned' => false,
            'banned_at' => null,
        ]);

        $user = User::find($userId);
        $this->sendSystemMessage($group->chat->id, ($user->name ?? 'User') . ' was unbanned.');
    }

    public function muteParticipant(LiveChatGroup $group, int $userId, ?int $minutes = null): void
    {
        if (!$group->chat) throw new \Exception('Live chat group has no associated chat.');

        $participant = UserChatParticipant::where('chat_id', $group->chat->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) throw new \Exception('User is not a participant.');

        $participant->update([
            'is_muted' => true,
            'muted_until' => $minutes ? now()->addMinutes($minutes) : null,
        ]);

        $user = User::find($userId);
        $this->sendSystemMessage($group->chat->id, ($user->name ?? 'User') . ' was muted.');
    }

    public function unmuteParticipant(LiveChatGroup $group, int $userId): void
    {
        if (!$group->chat) throw new \Exception('Live chat group has no associated chat.');

        $participant = UserChatParticipant::where('chat_id', $group->chat->id)
            ->where('user_id', $userId)
            ->first();

        if (!$participant) throw new \Exception('User is not a participant.');

        $participant->update([
            'is_muted' => false,
            'muted_until' => null,
        ]);
    }

    public function checkCanSend(User $user, UserChat $chat): void
    {
        $participant = UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            throw new \Exception('You are not a participant of this chat.');
        }

        if ($participant->is_banned) {
            throw new \Exception('You have been banned from this group.');
        }

        if ($participant->is_muted) {
            if ($participant->muted_until && $participant->muted_until->isFuture()) {
                throw new \Exception('You are muted until ' . $participant->muted_until->format('Y-m-d H:i') . '.');
            }
            if (!$participant->muted_until) {
                throw new \Exception('You have been muted indefinitely.');
            }
        }

        if ($chat->chat_mode === 'admin_only' && !$user->hasRole('admin')) {
            throw new \Exception('Only admins can send messages in this group.');
        }
    }

    protected function sendSystemMessage(int $chatId, string $content): void
    {
        $msg = UserChatMessage::create([
            'chat_id' => $chatId,
            'sender_id' => null,
            'content' => $content,
            'message_type' => 'system',
            'delivered_at' => now(),
        ]);

        try {
            broadcast(new \App\Events\Chat\MessageSent($msg))->toOthers();
        } catch (\Exception $e) {
            Log::error('LiveChatGroup broadcast error: ' . $e->getMessage());
        }
    }
}
