@extends('admin.layouts.app')

@section('title', 'Users')
@section('content')

    <div class="mx-auto space-y-6 max-w-7xl">
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
            <!-- Trash Users Button -->
            <a href="{{ route('admin.users.trashed') }}"
                class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                Trash Users
            </a>
        </div>

        {{-- Check if there is a success message --}}
        @if (session('success'))
            <div id="flash-message"
                class="fixed z-50 flex items-center justify-between w-full max-w-xs p-4 text-white transition-all duration-300 bg-green-500 rounded-lg shadow-lg top-4 right-4"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" {{-- Automatically hide after 5 seconds --}} x-transition>
                <span>{{ session('success') }}</span>

                {{-- Close button --}}
                <button @click="show = false" class="ml-4 text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Check if there is an error message --}}
        @if (session('error'))
            <div id="flash-message"
                class="fixed z-50 flex items-center justify-between w-full max-w-xs p-4 text-white transition-all duration-300 bg-red-500 rounded-lg shadow-lg top-4 right-4"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" {{-- Automatically hide after 5 seconds --}} x-transition>
                <span>{{ session('error') }}</span>

                {{-- Close button --}}
                <button @click="show = false" class="ml-4 text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        @endif


        <div class="overflow-x-auto bg-white rounded-lg shadow">

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">User</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Email</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Followers</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Followings</th>
                        {{-- <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Role</th> --}}
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Joined</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        @if ($user->hasRole('admin'))
                            @continue
                        @endif
                        <tr>
                            <td class="flex items-center gap-3 px-4 py-3">
                                <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                    alt="avatar" class="object-cover w-10 h-10 rounded-full">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->phone ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('admin.user.follower', $user) }}">
                                    {{ $user->followers_count ?? '0' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <a href="{{ route('admin.user.following', $user) }}">
                                    {{ $user->following_count ?? '0' }}
                                </a>
                            </td>
                            {{-- <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $user->roles->pluck('name')->join(', ') ?? 'User' }}</td> --}}
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $user->created_at->format('d M, Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->is_blocked)
                                    <span class="px-2 py-1 text-xs text-red-700 bg-red-100 rounded">Blocked</span>
                                @elseif(isset($user->is_verified) && $user->is_verified)
                                    <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded">Verified</span>
                                @elseif(isset($user->status) && $user->status === 'inactive')
                                    <span class="px-2 py-1 text-xs text-yellow-700 bg-yellow-100 rounded">Inactive</span>
                                @else
                                    <span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 space-x-2 text-right">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="inline-block px-3 py-1 text-sm text-blue-700 rounded bg-blue-50">View</a>

                                @if (!$user->is_blocked)
                                    <form action="{{ route('admin.users.block', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Block this user?')"
                                            class="px-3 py-1 text-sm text-red-700 rounded bg-red-50">Block</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Unblock this user?')"
                                            class="px-3 py-1 text-sm text-green-700 rounded bg-green-50">Unblock</button>
                                    </form>
                                @endif

                                @if (!$user->is_verified)
                                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('Verify this user?')"
                                            class="px-3 py-1 text-sm text-indigo-700 rounded bg-indigo-50">Verify</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.unverify', $user) }}" method="POST"
                                        class="inline">
                                        @csrf @method('PUT')
                                        <button type="submit" onclick="return confirm('UnVerify this user?')"
                                            class="px-3 py-1 text-sm text-red-700 rounded bg-red-50">Unverify</button>
                                    </form>
                                @endif

                                @if (!$user->is_seller)
                                    <form action="{{ route('admin.users.seller.toggle', $user) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            onclick="return confirm('Enable seller status for this user?')"
                                            class="px-3 py-1 text-sm text-indigo-700 rounded bg-indigo-50">
                                            Enable Seller
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.seller.toggle', $user) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            onclick="return confirm('Disable seller status for this user?')"
                                            class="px-3 py-1 text-sm text-red-700 rounded bg-red-50">
                                            Disable Seller
                                        </button>
                                    </form>
                                @endif
                                @if (!$user->is_employer)
                                    <form action="{{ route('admin.users.employer.toggle', $user) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            onclick="return confirm('Enable Employer status for this user?')"
                                            class="px-3 py-1 text-sm text-indigo-700 rounded bg-indigo-50">
                                            Enable Employer
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.employer.toggle', $user) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            onclick="return confirm('Disable Employer status for this user?')"
                                            class="px-3 py-1 text-sm text-red-700 rounded bg-red-50">
                                            Disable Employer
                                        </button>
                                    </form>
                                @endif


                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Permanently delete this user?')"
                                        class="px-3 py-1 text-sm text-gray-700 rounded bg-gray-50">Delete</button>
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
