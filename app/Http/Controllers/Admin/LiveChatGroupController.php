<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLiveChatGroupRequest;
use App\Models\Chat\UserChatParticipant;
use App\Models\LiveChatGroup;
use App\Models\User;
use App\Services\LiveChatGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LiveChatGroupController extends Controller
{
    protected LiveChatGroupService $liveChatGroupService;

    public function __construct(LiveChatGroupService $liveChatGroupService)
    {
        $this->liveChatGroupService = $liveChatGroupService;
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
}
