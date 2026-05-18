@extends('admin.layouts.app')
@section('title', $liveChatGroup->name)
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">{{ $liveChatGroup->name }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.live-chat-groups.edit', $liveChatGroup) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Edit</a>
            <form action="{{ route('admin.live-chat-groups.destroy', $liveChatGroup) }}" method="POST" onsubmit="return confirm('Delete this group permanently?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-3">Group Details</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Chat Mode</dt><dd><span class="px-2 py-1 rounded text-xs font-medium {{ $liveChatGroup->chat_mode === 'admin_only' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">{{ $liveChatGroup->chat_mode }}</span></dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd><span class="px-2 py-1 rounded text-xs font-medium {{ $liveChatGroup->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $liveChatGroup->is_active ? 'Active' : 'Inactive' }}</span></dd></div>
                    <div><dt class="text-gray-500">Created By</dt><dd>{{ $liveChatGroup->creator?->name ?? 'N/A' }}</dd></div>
                    <div><dt class="text-gray-500">Created</dt><dd>{{ $liveChatGroup->created_at->format('M d, Y g:i A') }}</dd></div>
                </dl>
                @if($liveChatGroup->description)
                    <div class="mt-3"><dt class="text-gray-500 text-sm">Description</dt><dd class="text-sm mt-1">{{ $liveChatGroup->description }}</dd></div>
                @endif
            </div>

            @if($liveChatGroup->chat)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-3">
                    <a href="{{ route('admin.live-chat-groups.show', $liveChatGroup) }}#chat" class="text-blue-600 hover:underline">Live Chat</a>
                </h3>
                <p class="text-sm text-gray-500">Chat ID: #{{ $liveChatGroup->chat->id }} | Participants: {{ $participants->count() }}</p>
                <p class="text-sm text-gray-400 mt-1">Messages can be viewed from the API or by clicking into the chat.</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-3">Participants ({{ $participants->count() }})</h3>

                <div class="mb-4 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Total registered users: <strong>{{ \App\Models\User::count() }}</strong>
                        @php $notAdded = \App\Models\User::count() - $participants->count(); @endphp
                        @if($notAdded > 0)
                            <br><span class="text-orange-600">{{ $notAdded }} users not yet added</span>
                        @endif
                    </div>
                    <form action="{{ route('admin.live-chat-groups.sync-users', $liveChatGroup) }}" method="POST" onsubmit="return confirm('Add all {{ \App\Models\User::count() - $participants->count() }} remaining users to this group?')">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Sync All Users
                        </button>
                    </form>
                </div>

                <div class="space-y-3">
                    @forelse($participants as $participant)
                    <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50 {{ $participant->is_banned ? 'opacity-50' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                                @if($participant->user->profile_photo_url)
                                    <img src="{{ $participant->user->profile_photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-500">{{ strtoupper(substr($participant->user->name, 0, 1)) }}</div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium truncate">{{ $participant->user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    @if($participant->is_admin) <span class="text-yellow-600">Admin</span>
                                    @elseif($participant->is_banned) <span class="text-red-500">Banned</span>
                                    @elseif($participant->is_muted) <span class="text-orange-500">Muted</span>
                                    @else Member @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-1 flex-shrink-0">
                            @if(!$participant->is_admin)
                            <form action="{{ route('admin.live-chat-groups.remove-participant', $liveChatGroup) }}" method="POST" class="inline" onsubmit="return confirm('Remove this user?')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                <button type="submit" class="text-xs text-red-600 hover:underline" title="Remove">Remove</button>
                            </form>
                            @if(!$participant->is_banned)
                            <form action="{{ route('admin.live-chat-groups.ban-participant', $liveChatGroup) }}" method="POST" class="inline" onsubmit="return confirm('Ban this user?')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                <button type="submit" class="text-xs text-orange-600 hover:underline" title="Ban">Ban</button>
                            </form>
                            @else
                            <form action="{{ route('admin.live-chat-groups.unban-participant', $liveChatGroup) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                <button type="submit" class="text-xs text-green-600 hover:underline" title="Unban">Unban</button>
                            </form>
                            @endif
                            @if(!$participant->is_muted)
                            <form action="{{ route('admin.live-chat-groups.mute-participant', $liveChatGroup) }}" method="POST" class="inline" onsubmit="return confirm('Mute this user?')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $participant->user_id }}">
                                <input type="hidden" name="minutes" value="60">
                                <button type="submit" class="text-xs text-gray-600 hover:underline" title="Mute 1hr">Mute</button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400">No participants.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
