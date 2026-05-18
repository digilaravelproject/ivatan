@extends('admin.layouts.app')
@section('title', 'Create Live Chat Group')
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Create Live Chat Group</h2>

    <form action="{{ route('admin.live-chat-groups.store') }}" method="POST" class="max-w-2xl bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border rounded-lg px-3 py-2 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Chat Mode *</label>
            <select name="chat_mode" class="w-full border rounded-lg px-3 py-2 @error('chat_mode') border-red-500 @enderror">
                <option value="everyone" {{ old('chat_mode') === 'everyone' ? 'selected' : '' }}>Everyone (all can send)</option>
                <option value="admin_only" {{ old('chat_mode') === 'admin_only' ? 'selected' : '' }}>Admin Only (only admins can send)</option>
            </select>
            @error('chat_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Group</button>
            <a href="{{ route('admin.live-chat-groups.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
@endsection
