@extends('admin.layouts.app')
@section('title', 'User: ' . $user->name)
@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    <!-- User Profile Section -->
    <div class="bg-white rounded-xl shadow p-6 flex gap-6">
        <!-- Profile Photo -->
        <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
            class="w-28 h-28 rounded-full object-cover">

        <!-- User Information -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <div class="text-sm text-gray-500">{{ $user->email }}</div>
            <div class="mt-3 flex items-center gap-3">
                <span class="text-xs text-gray-400">Joined: {{ $user->created_at->format('d M, Y') }}</span>
                <span class="text-xs text-gray-400">Posts: {{ $user->posts()->count() ?? 0 }}</span>
            </div>
        </div>

        <!-- Block/Unblock and Verify/Unverify Actions -->
        <div class="flex flex-col gap-2">
            @if (!$user->is_blocked)
                <form action="{{ route('admin.users.block', $user) }}" method="POST">@csrf @method('PUT')
                    <button class="px-4 py-2 bg-red-50 text-red-700 rounded">Block</button>
                </form>
            @else
                <form action="{{ route('admin.users.unblock', $user) }}" method="POST">@csrf @method('PUT')
                    <button class="px-4 py-2 bg-green-50 text-green-700 rounded">Unblock</button>
                </form>
            @endif

            @if (!$user->is_verified)
                <form action="{{ route('admin.users.verify', $user) }}" method="POST">@csrf @method('PUT')
                    <button class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded">Verify</button>
                </form>
            @endif

            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">@csrf @method('DELETE')
                <button class="px-4 py-2 bg-gray-50 text-gray-700 rounded">Delete</button>
            </form>
        </div>
    </div>

    <!-- Recent Posts Section -->
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">Recent Posts</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($user->posts ?? collect() as $post)
                @php
                    $media = is_array($post->media_metadata) ? $post->media_metadata : json_decode($post->media_metadata, true);
                    $images = $media['images'] ?? [];
                    $videos = $media['videos'] ?? [];
                    $postType = $post->type ?? 'image'; // Default to 'image' type if no type is set
                @endphp

                <!-- Post Card -->
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <!-- Post Header: Caption and Post Time -->
                    <div class="p-4">
                        <p class="font-semibold text-gray-800">{{ $post->caption ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>

                    <!-- Post Media: Handle Carousel for Images -->
                    <div class="w-full h-48 bg-gray-200">
                        @if ($postType === 'carousel' || count($images) > 0 || count($videos) > 0)
                            @if ($postType === 'carousel')
                                <!-- Slick Carousel -->
                                <div class="carousel-slider">
                                    @foreach ($images as $imageUrl)
                                        <img src="{{ (filter_var($imageUrl, FILTER_VALIDATE_URL)) ? $imageUrl : asset('storage/' . $imageUrl) }}"
                                             alt="carousel image" class="w-full h-full object-cover" loading="lazy">
                                    @endforeach
                                    @foreach ($videos as $videoUrl)
                                        <video width="100%" height="auto" controls>
                                            <source src="{{ (filter_var($videoUrl, FILTER_VALIDATE_URL)) ? $videoUrl : asset('storage/' . $videoUrl) }}" type="video/mp4">
                                        </video>
                                    @endforeach
                                </div>
                            @elseif ($postType === 'image')
                                <img src="{{ (filter_var($images[0], FILTER_VALIDATE_URL)) ? $images[0] : asset('storage/' . $images[0]) }}"
                                     alt="post image" class="w-full h-full object-cover" loading="lazy">
                            @elseif ($postType === 'video')
                                <video width="100%" height="auto" controls>
                                    <source src="{{ (filter_var($videos[0], FILTER_VALIDATE_URL)) ? $videos[0] : asset('storage/' . $videos[0]) }}" type="video/mp4">
                                </video>
                            @endif
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="default image" class="w-full h-full object-cover" loading="lazy">
                        @endif
                    </div>

                    <!-- Post Footer: Likes and Comments -->
                    <div class="p-4 flex justify-between items-center text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-thumbs-up mr-2"></i> <!-- Like icon -->
                            <span>{{ $post->likes()->count() }} Likes</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-comment-dots mr-2"></i> <!-- Comment icon -->
                            <span>{{ $post->comments()->count() }} Comments</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 col-span-4">No posts available</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
