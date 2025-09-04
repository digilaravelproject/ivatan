@extends('admin.layouts.app')
@section('title', 'User: ' . $user->name)
@section('content')

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow p-6 flex gap-6">
            <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                class="w-28 h-28 rounded-full object-cover">
            <div class="flex-1">
                <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                <div class="mt-3 flex items-center gap-3">
                    <span class="text-xs text-gray-400">Joined: {{ $user->created_at->format('d M, Y') }}</span>
                    <span class="text-xs text-gray-400">Posts: {{ $user->posts()->count() ?? 0 }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                @if (!$user->is_blocked)
                    <form action="{{ route('admin.users.block', $user) }}" method="POST">@csrf @method('PUT')<button
                            class="px-4 py-2 bg-red-50 text-red-700 rounded">Block</button></form>
                @else
                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST">@csrf @method('PUT')<button
                            class="px-4 py-2 bg-green-50 text-green-700 rounded">Unblock</button></form>
                @endif

                @if (!$user->is_verified)
                    <form action="{{ route('admin.users.verify', $user) }}" method="POST">@csrf @method('PUT')<button
                            class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded">Verify</button></form>
                @endif

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">@csrf @method('DELETE')<button
                        class="px-4 py-2 bg-gray-50 text-gray-700 rounded">Delete</button></form>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-3">Recent Posts</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                @forelse($user->posts ?? collect() as $post)
                    @php
                        $media = json_decode($post->media_metadata, true);
                        $imageUrl = $media['url'] ?? asset('images/default-avatar.png');
                    @endphp
                    <li class="border-b pb-2">{{ $post->caption ?? '—' }} <span
                            class="text-xs text-gray-400">({{ $post->created_at->diffForHumans() }})</span></li>
                    <li class="border-b pb-2">
                        <img src="{{ $imageUrl }}" alt="media image" class="w-50 h-50 rounded-full object-cover">
                        <span class="text-xs text-gray-400">({{ $post->created_at->diffForHumans() }})</span>
                    </li>
                @empty
                    <li class="text-gray-400">No posts</li>
                @endforelse
            </ul>
        </div>

        <!-- you can add Reels / Products / Applications similarly -->
    </div>

@endsection
