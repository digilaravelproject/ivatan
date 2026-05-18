@extends('admin.layouts.app')
@section('title', 'Send Notification')
@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Notifications</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Send Notification</h2>

        <div x-data="{ type: 'single' }">
            <div class="mb-4 flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" x-model="type" value="single" class="form-radio text-blue-600">
                    <span class="ml-2 text-sm font-medium">To Single User</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" x-model="type" value="broadcast" class="form-radio text-blue-600">
                    <span class="ml-2 text-sm font-medium">Broadcast to All</span>
                </label>
            </div>

            {{-- Single User Form --}}
            <form action="{{ route('admin.notifications.send') }}" method="POST" x-show="type === 'single'">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Select User</label>
                        <select name="user_id" required class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">-- Select User --</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    @include('admin.notifications._form_fields')
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Send to User</button>
                </div>
            </form>

            {{-- Broadcast Form --}}
            <form action="{{ route('admin.notifications.broadcast') }}" method="POST" x-show="type === 'broadcast'">
                @csrf
                <div class="space-y-4">
                    <div class="p-3 bg-yellow-50 text-yellow-700 rounded-lg text-sm">
                        <strong>Warning:</strong> This will send a notification to <strong>{{ \App\Models\User::where('is_blocked', 0)->count() }}</strong> active users.
                    </div>
                    @include('admin.notifications._form_fields')
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Broadcast to All Users</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
