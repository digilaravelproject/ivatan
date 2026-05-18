@extends('admin.layouts.app')
@section('title', 'Notification Detail')
@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.notifications.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Notifications</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Notification Detail</h2>

        <dl class="grid grid-cols-1 gap-4 text-sm">
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">ID</dt>
                <dd class="col-span-2 font-mono text-xs">{{ $notification->id }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">User</dt>
                <dd class="col-span-2">{{ $notification->user_name }} ({{ $notification->user_email }})</dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">Category</dt>
                <dd class="col-span-2"><span class="px-2 py-1 rounded text-xs font-medium bg-gray-100">{{ $data['category'] ?? 'N/A' }}</span></dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">Title</dt>
                <dd class="col-span-2 font-semibold">{{ $data['payload']['title'] ?? 'N/A' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">Message</dt>
                <dd class="col-span-2">{{ $data['payload']['message'] ?? 'N/A' }}</dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">Status</dt>
                <dd class="col-span-2">
                    @if(is_null($notification->read_at))
                        <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Unread</span>
                    @else
                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Read at {{ \Carbon\Carbon::parse($notification->read_at)->format('M d, Y g:i A') }}</span>
                    @endif
                </dd>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <dt class="font-medium text-gray-500">Created</dt>
                <dd class="col-span-2">{{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y g:i A') }}</dd>
            </div>
        </dl>

        <div class="mt-6 border-t pt-6">
            <h3 class="text-lg font-semibold mb-3">Full Payload</h3>
            <pre class="bg-gray-50 p-4 rounded-lg text-xs overflow-auto max-h-96">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>
@endsection
