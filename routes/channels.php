<?php

use App\Models\Chat\UserChatParticipant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['web', 'admin']]);

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    if ($user->is_admin || $user->hasRole('admin')) {
        return true;
    }

    return UserChatParticipant::where('chat_id', $chatId)->where('user_id', $user->id)->exists();
}, ['guards' => ['web', 'admin']]);

// User private notification & calling channel
Broadcast::channel('private-user.{id}', function ($user, int $id) {
    return (int) $user->id === $id;
}, ['guards' => ['sanctum']]);

// Chat Presence Channel
Broadcast::channel('presence-chat.{chatId}', function ($user, int $chatId) {
    // 1. Participant Validation
    $participant = UserChatParticipant::where('chat_id', $chatId)
        ->where('user_id', $user->id)
        ->first();

    if (!$participant) {
        return false;
    }

    // 2. Extra Muted/Banned Checks for Live Group chats
    if ($participant->is_banned) {
        return false;
    }

    // Return user status profile for presence channel tracking
    return [
        'id' => $user->id,
        'name' => $user->name,
        'username' => $user->username,
        'profile_photo_url' => $user->profile_photo_url,
    ];
}, ['guards' => ['sanctum']]);

