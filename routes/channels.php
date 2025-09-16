<?php

use App\Models\Chat\UserChatParticipant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return UserChatParticipant::where('chat_id', $chatId)->where('user_id', $user->id)->exists();
});
