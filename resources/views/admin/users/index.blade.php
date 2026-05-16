@extends('admin.layouts.app')
@section('title', 'Users')
@section('content')

    <div class="p-6 mx-auto space-y-8 bg-white rounded-lg shadow max-w-7xl">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Users</h2>
            <form method="GET" class="flex items-center gap-2" action="{{ route('admin.users.index') }}">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name / email / phone"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring" />
                <select name="status" class="px-3 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="active" @if (($status ?? '') === 'active') selected @endif>Active</option>
                    <option value="inactive" @if (($status ?? '') === 'inactive') selected @endif>Inactive</option>
                    <option value="blocked" @if (($status ?? '') === 'blocked') selected @endif>Blocked</option>
                    <option value="verified" @if (($status ?? '') === 'verified') selected @endif>Verified</option>
                </select>
                <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-lg">Filter</button>
            </form>
            <a href="{{ route('admin.users.trashed') }}"
                class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                Trash Users
            </a>
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">User</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Email</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Joined</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="flex items-center gap-3 px-4 py-3">
                                <img src="{{ $user->profile_photo_url }}" alt="avatar"
                                    class="object-cover w-10 h-10 rounded-full"
                                    onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->phone ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->created_at->format('d M, Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->is_blocked)
                                    <span class="px-2 py-1 text-xs text-red-700 bg-red-100 rounded">Blocked</span>
                                @elseif($user->is_verified)
                                    <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded">Verified</span>
                                @else
                                    <span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-2 text-right">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="inline-block px-3 py-1 text-sm text-blue-700 rounded bg-blue-50">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection