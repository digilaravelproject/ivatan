@extends('admin.layouts.app')
@section('title', 'Live Chat Groups')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Live Chat Groups</h2>
        <a href="{{ route('admin.live-chat-groups.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Create Group
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chat Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($groups as $group)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $group->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $group->chat_mode === 'admin_only' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                            {{ $group->chat_mode }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $group->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $group->creator?->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $group->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2 flex items-center">
                        <a href="{{ route('admin.live-chat-groups.show', $group) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('admin.live-chat-groups.edit', $group) }}" class="text-indigo-600 hover:underline">Edit</a>
                        @if($group->chat)
                        <a href="{{ route('admin.live-chat-groups.chat', $group) }}" class="text-green-600 hover:underline">Chat</a>
                        @endif
                        <form action="{{ route('admin.live-chat-groups.sync-users', $group) }}" method="POST" class="inline" onsubmit="return confirm('Add all users to this group?')">
                            @csrf
                            <button type="submit" class="text-green-600 hover:underline text-sm">Sync</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-400">No live chat groups found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $groups->links() }}</div>
</div>
@endsection
