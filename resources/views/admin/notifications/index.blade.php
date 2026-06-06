@extends('admin.layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Notifications</h2>
        <a href="{{ route('admin.notifications.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Send Notification
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    @if(isset($queueWorkerDown) && $queueWorkerDown)
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-medium text-red-800">Queue worker is not running!</p>
                    <p class="text-sm text-red-600 mt-1">Notifications will not be delivered. Run <code class="px-1 bg-red-100 rounded">php artisan queue:work</code> or check Supervisor status.</p>
                </div>
            </div>
        </div>
    @endif

    <form method="GET" class="mb-4 flex gap-3 flex-wrap">
        <input type="text" name="search" placeholder="Search by user name or email..." value="{{ request('search') }}" class="rounded-lg border-gray-300 text-sm flex-1 min-w-[200px]">
        <select name="category" class="rounded-lg border-gray-300 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <select name="only" class="rounded-lg border-gray-300 text-sm">
            <option value="">All</option>
            <option value="unread" {{ request('only') === 'unread' ? 'selected' : '' }}>Unread Only</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">Filter</button>
        <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Reset</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notifications as $n)
                <tr class="{{ is_null($n['read_at']) ? 'bg-blue-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="font-medium">{{ $n['user_name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $n['user_email'] }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $n['category'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm max-w-[250px] truncate">{{ $n['title'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if(is_null($n['read_at']))
                            <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">Unread</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">Read</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($n['created_at'])->diffForHumans() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.notifications.show', $n['id']) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-400">No notifications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
