@extends('admin.layouts.app')
@section('title', 'Edit Live Chat Group')
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Edit: {{ $liveChatGroup->name }}</h2>

    <form action="{{ route('admin.live-chat-groups.update', $liveChatGroup) }}" method="POST" class="max-w-2xl bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
            <input type="text" name="name" value="{{ old('name', $liveChatGroup->name) }}" required
                class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $liveChatGroup->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Chat Mode *</label>
            <select name="chat_mode" class="w-full border rounded-lg px-3 py-2 @error('chat_mode') border-red-500 @enderror">
                <option value="everyone" {{ old('chat_mode', $liveChatGroup->chat_mode) === 'everyone' ? 'selected' : '' }}>Everyone (all can send)</option>
                <option value="admin_only" {{ old('chat_mode', $liveChatGroup->chat_mode) === 'admin_only' ? 'selected' : '' }}>Admin Only (only admins can send)</option>
            </select>
            @error('chat_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $liveChatGroup->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update Group</button>
            <a href="{{ route('admin.live-chat-groups.show', $liveChatGroup) }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
