@extends('admin.layouts.app')
@section('title', 'All Posts')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
                            {{-- <pre>

Image URL: {{ print_r($posts) }}
</pre> --}}
        <h2 class="mb-4 text-2xl font-bold">All Posts</h2>

        <div class="p-6 bg-white shadow rounded-xl">
            @if ($posts->count())
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                    @foreach ($posts as $post)
                        <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                            <!-- Post Header -->
                            <div class="flex items-center p-4">
                                <img src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                    alt="user profile" class="object-cover w-10 h-10 rounded-full">

                                <div class="ml-3">
                                    <a href="{{ route('admin.users.show', $post->user->id) }}">
                                        <p class="text-sm font-semibold text-gray-800">{{ $post->user->name }}</p>
                                    </a>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <!-- Media Section -->
                            <!-- Post Media -->
                            {{-- <pre>
Images Count: {{ $post->getMedia('image')->count() }}
Image URL: {{ $post }}
</pre> --}}

                            <a href="{{ url("admin/user-posts/{$post->id}") }}">
                                @php
                                    $imageUrl = $post->getFirstMediaUrl('images');
                                @endphp

                              @if (!empty($post->images) && isset($post->images[0]['original_url']))
    <img src="{{ $post->images[0]['original_url'] }}"
         alt="Post Image"
         class="object-cover w-full h-64" loading="lazy">
@else
    <img src="{{ asset('images/default-image.png') }}"
         alt="Default Image"
         class="object-cover w-full h-64" loading="lazy">
@endif

                            </a>


                            {{-- <pre>{{ print_r($post->media->pluck('original_url'), true) }}</pre> --}}



                            <!-- Caption -->
                            <div class="px-4 py-2">
                                <p class="text-sm text-gray-700">{{ $post->caption }}</p>
                            </div>

                            <!-- Post Footer -->
                            <div class="flex items-center justify-between px-4 py-2 text-sm text-gray-600 border-t">
                                <!-- Likes -->
                                <a href="{{ route('admin.post.likes', $post->id) }}"
                                    class="flex items-center space-x-1 hover:text-blue-600">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>{{ $post->likes_count }} Likes</span>
                                </a>

                                <!-- Comments -->
                                <a href="{{ route('admin.post.comments', $post->id) }}"
                                    class="flex items-center space-x-1 hover:text-blue-600">
                                    <i class="fas fa-comment-dots"></i>
                                    <span>{{ $post->comments_count }} Comments</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $posts->links('pagination::tailwind') }}
                </div>
            @else
                <div class="py-12 text-center text-gray-500">No posts available.</div>
            @endif
        </div>
    </div>
@endsection
