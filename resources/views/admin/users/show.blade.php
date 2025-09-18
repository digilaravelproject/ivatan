@extends('admin.layouts.app')
@section('title', 'User: ' . $user->name)
@section('content')

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- User Profile Section -->
        <div class="flex gap-6 p-6 bg-white shadow rounded-xl">
            <!-- Profile Photo -->
            <img src="{{ $user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                class="object-cover rounded-full w-28 h-28">

            <!-- User Information -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                <div class="flex items-center gap-3 mt-3">
                    <span class="text-xs text-gray-400">Joined: {{ $user->created_at->format('d M, Y') }}</span>
                    <span class="text-xs text-gray-400">Posts: {{ $user->posts()->count() ?? 0 }}</span>
                </div>
            </div>

            <!-- Block/Unblock and Verify/Unverify Actions -->
            <div class="flex flex-col gap-2">
                @if (!$user->is_blocked)
                    <form action="{{ route('admin.users.block', $user) }}" method="POST">@csrf @method('PUT')
                        <button class="px-4 py-2 text-red-700 rounded bg-red-50">Block</button>
                    </form>
                @else
                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST">@csrf @method('PUT')
                        <button class="px-4 py-2 text-green-700 rounded bg-green-50">Unblock</button>
                    </form>
                @endif

                @if (!$user->is_verified)
                    <form action="{{ route('admin.users.verify', $user) }}" method="POST">@csrf @method('PUT')
                        <button class="px-4 py-2 text-indigo-700 rounded bg-indigo-50">Verify</button>
                    </form>
                @endif

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">@csrf @method('DELETE')
                    <button class="px-4 py-2 text-gray-700 rounded bg-gray-50">Delete</button>
                </form>
            </div>
        </div>

        <!-- Recent Posts Section -->
        <div class="p-4 bg-white rounded shadow">
            <h3 class="mb-3 font-semibold">Recent Posts</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($user->posts ?? collect() as $post)
                    @php
                        $media = is_array($post->media_metadata)
                            ? $post->media_metadata
                            : json_decode($post->media_metadata, true);
                        $images = $media['images'] ?? [];
                        $videos = $media['videos'] ?? [];
                        $postType = $post->type ?? 'image'; // Default to 'image' type if no type is set
                    @endphp

                    <!-- Post Card -->
                    <div class="overflow-hidden border rounded-lg shadow-sm">
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
                                            <img src="{{ filter_var($imageUrl, FILTER_VALIDATE_URL) ? $imageUrl : asset('storage/' . $imageUrl) }}"
                                                alt="carousel image" class="object-cover w-full h-full" loading="lazy">
                                        @endforeach
                                        @foreach ($videos as $videoUrl)
                                            <video width="100%" height="auto" controls>
                                                <source
                                                    src="{{ filter_var($videoUrl, FILTER_VALIDATE_URL) ? $videoUrl : asset('storage/' . $videoUrl) }}"
                                                    type="video/mp4">
                                            </video>
                                        @endforeach
                                    </div>
                                @elseif ($postType === 'image')
                                    <img src="{{ filter_var($images[0], FILTER_VALIDATE_URL) ? $images[0] : asset('storage/' . $images[0]) }}"
                                        alt="post image" class="object-cover w-full h-full" loading="lazy">
                                @elseif ($postType === 'video')
                                    <video width="100%" height="auto" controls>
                                        <source
                                            src="{{ filter_var($videos[0], FILTER_VALIDATE_URL) ? $videos[0] : asset('storage/' . $videos[0]) }}"
                                            type="video/mp4">
                                    </video>
                                @endif
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" alt="default image"
                                    class="object-cover w-full h-full" loading="lazy">
                            @endif
                        </div>

                        <!-- Post Footer: Likes and Comments -->
                        <div class="flex items-center justify-between p-4 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <a href="#" aria-label="Like this post"
                                    class="flex items-center text-sm text-blue-500 transition-all duration-200 ease-in-out hover:text-blue-700">
                                    <i class="mr-1 fas fa-thumbs-up"></i>
                                    <span>Likes</span>

                                </a>
                            </div>

                            <div class="flex items-center space-x-2">
                                <a href="#" aria-label="Like this post"
                                    class="flex items-center text-sm text-blue-500 transition-all duration-200 ease-in-out hover:text-blue-700">
                                    <i class="mr-1 fas fa-comment-dots"></i> <!-- Comment icon -->
                                    <span> Comments</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center text-gray-400">No posts available</div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
