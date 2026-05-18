<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLiveChatGroupRequest;
use App\Models\Chat\UserChatMessage;
use App\Models\Chat\UserChatParticipant;
use App\Models\LiveChatGroup;
use App\Models\User;
use App\Services\ChatService;
use App\Services\LiveChatGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LiveChatGroupController extends Controller
{
    protected LiveChatGroupService $liveChatGroupService;
    protected ChatService $chatService;

    public function __construct(
        LiveChatGroupService $liveChatGroupService,
        ChatService $chatService
    ) {
        $this->liveChatGroupService = $liveChatGroupService;
        $this->chatService = $chatService;
    }

    public function index(Request $request)
    {
        $query = LiveChatGroup::with('creator');

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $groups = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.live-chat-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.live-chat-groups.create');
    }

    public function store(StoreLiveChatGroupRequest $request)
    {
        try {
            $group = $this->liveChatGroupService->create(
                $request->validated(),
                Auth::user()
            );
            return redirect()->route('admin.live-chat-groups.show', $group)
                ->with('success', 'Live chat group created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create live chat group: ' . $e->getMessage());
            return back()->with('error', 'Failed to create group: ' . $e->getMessage())->withInput();
        }
    }

    public function show(LiveChatGroup $liveChatGroup)
    {
        $liveChatGroup->load(['creator', 'chat.participants.user']);

        $participants = collect();
        if ($liveChatGroup->chat) {
            $participants = UserChatParticipant::where('chat_id', $liveChatGroup->chat->id)
                ->with('user')
                ->orderByDesc('is_admin')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('admin.live-chat-groups.show', compact('liveChatGroup', 'participants'));
    }

    public function edit(LiveChatGroup $liveChatGroup)
    {
        return view('admin.live-chat-groups.edit', compact('liveChatGroup'));
    }

    public function update(StoreLiveChatGroupRequest $request, LiveChatGroup $liveChatGroup)
    {
        try {
            $this->liveChatGroupService->update($liveChatGroup, $request->validated());
            return redirect()->route('admin.live-chat-groups.show', $liveChatGroup)
                ->with('success', 'Live chat group updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update live chat group: ' . $e->getMessage());
            return back()->with('error', 'Failed to update group.')->withInput();
        }
    }

    public function destroy(LiveChatGroup $liveChatGroup)
    {
        try {
            if ($liveChatGroup->chat) {
                $liveChatGroup->chat->delete();
            }
            $liveChatGroup->delete();
            return redirect()->route('admin.live-chat-groups.index')
                ->with('success', 'Live chat group deleted.');
        } catch (\Exception $e) {
            Log::error('Failed to delete live chat group: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete group.');
        }
    }

    public function removeParticipant(Request $request, LiveChatGroup $liveChatGroup)
    {
        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        try {
            $this->liveChatGroupService->removeParticipant($liveChatGroup, $request->user_id);
            return back()->with('success', 'Participant removed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function banParticipant(Request $request, LiveChatGroup $liveChatGroup)
    {
        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        try {
            $this->liveChatGroupService->banParticipant($liveChatGroup, $request->user_id);
            return back()->with('success', 'Participant banned.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unbanParticipant(Request $request, LiveChatGroup $liveChatGroup)
    {
        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        try {
            $this->liveChatGroupService->unbanParticipant($liveChatGroup, $request->user_id);
            return back()->with('success', 'Participant unbanned.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function syncUsers(LiveChatGroup $liveChatGroup)
    {
        try {
            $added = $this->liveChatGroupService->syncGroupUsers($liveChatGroup);
            return back()->with('success', "Sync complete. {$added} new users added.");
        } catch (\Exception $e) {
            Log::error('Failed to sync users: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function muteParticipant(Request $request, LiveChatGroup $liveChatGroup)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'minutes' => 'nullable|integer|min:1|max:43200',
        ]);

        try {
            $this->liveChatGroupService->muteParticipant(
                $liveChatGroup,
                $request->user_id,
                $request->minutes
            );
            return back()->with('success', 'Participant muted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function chat(LiveChatGroup $liveChatGroup)
    {
        if (!$liveChatGroup->chat) {
            return redirect()->route('admin.live-chat-groups.show', $liveChatGroup)
                ->with('error', 'No chat associated with this group yet.');
        }

        $user = Auth::user();

        // Auto-join admin as participant if not already
        $isParticipant = $liveChatGroup->chat->participants()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isParticipant) {
            $liveChatGroup->chat->participants()->create([
                'user_id' => $user->id,
                'is_admin' => true,
            ]);
        }

        $liveChatGroup->load(['creator', 'chat.participants.user']);

        return view('admin.live-chat-groups.chat', [
            'group' => $liveChatGroup,
            'chat' => $liveChatGroup->chat,
            'user' => $user,
        ]);
    }

    public function fetchMessages(LiveChatGroup $liveChatGroup, Request $request)
    {
        if (!$liveChatGroup->chat) {
            return response()->json(['messages' => []]);
        }

        $query = UserChatMessage::where('chat_id', $liveChatGroup->chat->id)
            ->with('sender')
            ->orderByDesc('created_at');

        if ($request->after_id) {
            $query->where('id', '>', $request->after_id);
            $messages = $query->oldest()->get();
        } else {
            $messages = $query->limit(50)->get()->reverse()->values();
        }

        $messages->transform(function ($msg) {
            return [
                'id' => $msg->id,
                'chat_id' => $msg->chat_id,
                'sender_id' => $msg->sender_id,
                'content' => $msg->content,
                'message_type' => $msg->message_type,
                'created_at' => $msg->created_at->toISOString(),
                'sender' => $msg->sender ? [
                    'id' => $msg->sender->id,
                    'name' => $msg->sender->name,
                    'profile_photo_url' => $msg->sender->profile_photo_url,
                ] : null,
            ];
        });

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(Request $request, LiveChatGroup $liveChatGroup)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        if (!$liveChatGroup->chat) {
            return response()->json(['error' => 'No chat associated.'], 400);
        }

        try {
            $message = $this->chatService->sendMessage(
                Auth::user(),
                $liveChatGroup->chat,
                ['content' => $request->input('content'), 'message_type' => 'text']
            );

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'sender_id' => $message->sender_id,
                    'content' => $message->content,
                    'message_type' => $message->message_type,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => Auth::user()->id,
                        'name' => Auth::user()->name,
                        'profile_photo_url' => Auth::user()->profile_photo_url,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
