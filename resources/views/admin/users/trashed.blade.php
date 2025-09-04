@extends('admin.layouts.app')

@section('title', 'Users')
@section('content')

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Users</h2>
            <form method="GET" class="flex items-center gap-2" action="{{ route('admin.users.index') }}">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name / email / phone"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring" />
                <select name="status" class="px-3 py-2 border rounded-lg">
                    <option value="">All</option>
                    {{-- <option value="active" @if (($status ?? '') === 'active') selected @endif>Active</option> --}}
                    <option value="inactive" @if (($status ?? '') === 'inactive') selected @endif>Inactive</option>
                    <option value="blocked" @if (($status ?? '') === 'blocked') selected @endif>Blocked</option>
                    <option value="verified" @if (($status ?? '') === 'verified') selected @endif>Verified</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Filter</button>
            </form>

        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Joined</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3 flex items-center gap-3">
                                <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                    alt="avatar" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->phone ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $user->roles->pluck('name')->join(', ') ?? 'User' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->created_at->format('d M, Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->is_blocked)
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Blocked</span>
                                @elseif(isset($user->is_verified) && $user->is_verified)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Verified</span>
                                @elseif(isset($user->status) && $user->status === 'inactive')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Inactive</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded text-sm">View</a>

                                {{-- @if (!$user->is_blocked)
                                    <form action="{{ route('admin.users.block', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Block this user?')"
                                            class="px-3 py-1 bg-red-50 text-red-700 rounded text-sm">Block</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Unblock this user?')"
                                            class="px-3 py-1 bg-green-50 text-green-700 rounded text-sm">Unblock</button>
                                    </form>
                                @endif --}}

                                {{-- @if (!$user->is_verified)
                                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Verify this user?')"
                                            class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded text-sm">Verify</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.unverify', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('UnVerify this user?')"
                                            class="px-3 py-1 bg-red-50 text-red-700 rounded text-sm">Unverify</button>
                                    </form>
                                @endif --}}

                                <form action="{{ route('admin.users.restore', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Restore this user?')"
                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No users found.</td>
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
