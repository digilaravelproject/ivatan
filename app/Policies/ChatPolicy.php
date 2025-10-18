<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chat\UserChat;
use App\Models\Chat\UserChatParticipant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can add participants to the chat.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Chat\UserChat $chat
     * @return bool
     */
    public function addParticipant(User $user, UserChat $chat)
    {
        // The user must be the chat owner or an admin in the chat
        return UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->where(function ($query) use ($chat) {
                // Check if the user is an admin or the owner of the chat
                $query->where('is_admin', true)
                    ->orWhere('user_id', $chat->owner_id);
            })
            ->exists() || $chat->owner_id === $user->id; // Allow the owner to always add participants
    }

    // Check if the user is allowed to leave the group
    public function leaveGroup(User $user, UserChat $chat)
    {
        // The owner cannot leave the group
        if ($chat->owner_id === $user->id) {
            return false;
        }

        // Participants can leave their own group
        return UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function removeParticipant(User $user, UserChat $chat)
    {
        // The user must be an admin or the owner to remove someone
        return $chat->owner_id === $user->id || UserChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->exists();
    }
}
