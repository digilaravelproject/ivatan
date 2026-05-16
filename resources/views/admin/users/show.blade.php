@extends('admin.layouts.app')
@section('title', 'User: ' . $user->name)
@section('content')

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex gap-6 p-6 bg-white shadow rounded-xl">
            <div class="relative shrink-0">
                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                    class="object-cover border-4 border-white rounded-full shadow-sm w-28 h-28"
                    onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
            </div>

            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <div class="text-sm text-gray-500">{{ $user->email }}</div>

                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                        Joined: {{ $user->created_at->format('d M, Y') }}
                    </span>
                    <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                        Posts: {{ $user->posts_count ?? 0 }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                @if (!$user->is_blocked)
                    <form action="{{ route('admin.users.block', $user) }}" method="POST">@csrf @method('PUT')
                        <button
                            class="w-full px-4 py-2 text-sm font-medium text-red-700 transition rounded-lg bg-red-50 hover:bg-red-100">Block</button>
                    </form>
                @else
                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST">@csrf @method('PUT')
                        <button
                            class="w-full px-4 py-2 text-sm font-medium text-green-700 transition rounded-lg bg-green-50 hover:bg-green-100">Unblock</button>
                    </form>
                @endif

                @if (!$user->is_verified)
                    <form action="{{ route('admin.users.verify', $user) }}" method="POST">@csrf @method('PUT')
                        <button
                            class="w-full px-4 py-2 text-sm font-medium text-indigo-700 transition rounded-lg bg-indigo-50 hover:bg-indigo-100">Verify</button>
                    </form>
                @endif

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                    onsubmit="return confirm('Are you sure?');">@csrf @method('DELETE')
                    <button
                        class="w-full px-4 py-2 text-sm font-medium text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200">Delete</button>
                </form>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Recent Activity</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($user->posts ?? collect() as $post)
                    @php
                        $media = is_array($post->media_metadata) ? $post->media_metadata : json_decode($post->media_metadata, true);
                        $images = $media['images'] ?? [];
                        $videos = $media['videos'] ?? [];
                    @endphp

                    <div class="overflow-hidden transition border rounded-lg shadow-sm hover:shadow-md">
                        <div class="relative bg-gray-100 aspect-square group">
                            @if(count($images) > 0)
                                <img src="{{ filter_var($images[0], FILTER_VALIDATE_URL) ? $images[0] : Storage::url($images[0]) }}"
                                    class="object-cover w-full h-full" loading="lazy">
                            @elseif(count($videos) > 0)
                                <div class="flex items-center justify-center w-full h-full bg-black">
                                    <i class="text-2xl text-white fas fa-play"></i>
                                </div>
                            @else
                                <div class="flex items-center justify-center w-full h-full text-gray-400">
                                    <i class="text-2xl fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="text-sm font-medium truncate">{{ $post->caption ?? 'No Caption' }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 py-8 text-center text-gray-400 border-2 border-dashed rounded-lg">
                        No recent posts available
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection