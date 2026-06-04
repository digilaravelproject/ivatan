<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveChatGroupResource;
use App\Models\LiveChatGroup;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatGroupController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $user = Auth::user();

        $groups = LiveChatGroup::active()
            ->whereHas('chat.participants', fn($q) => $q->where('user_id', $user->id))
            ->with(['chat' => function ($q) use ($user) {
                $q->with(['participants.user', 'lastMessage.sender'])
                  ->withCount('participants');
            }])
            ->get();

        $data = $groups->map(function ($group) use ($user) {
            $chat = $group->chat;
            $participant = $chat?->participants->firstWhere('user_id', $user->id);

            return [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'description' => $group->description,
                'chat_mode' => $group->chat_mode,
                'is_active' => $group->is_active,
                'chat_id' => $chat?->id,
                'participants_count' => $chat?->participants_count ?? 0,
                'last_message' => $chat?->lastMessage,
                'is_banned' => $participant?->is_banned ?? false,
                'is_muted' => $participant?->is_muted ?? false,
                'created_at' => $group->created_at,
            ];
        });

        return $this->success(['groups' => $data]);
    }

    public function show(LiveChatGroup $liveChatGroup): JsonResponse
    {
        $user = Auth::user();

        if (!$liveChatGroup->is_active) {
            return $this->error('Group not found.', 404);
        }

        $isMember = $liveChatGroup->chat?->participants()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return $this->error('You are not a member of this group.', 403);
        }

        $liveChatGroup->load([
            'creator' => fn ($q) => $q->select(['id', 'name']),
            'chat'    => fn ($q) => $q->select(['id', 'uuid', 'name', 'type', 'chat_mode', 'live_chat_group_id']),
        ]);

        return $this->success([
            'group' => new LiveChatGroupResource($liveChatGroup),
        ]);
    }
}
