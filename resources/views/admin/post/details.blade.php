@extends('admin.layouts.app')

@section('title', 'Post Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Post Details Section -->
    <div class="p-6 bg-white rounded-lg shadow">
        <!-- Profile Section -->
        <div class="flex items-center mb-4">
            <img src="{{ $post['user']['profile_photo_path'] ?? asset('images/default-avatar.png') }}"
                alt="Profile Picture" class="object-cover w-12 h-12 rounded-full">
            <div class="ml-3">
                <p class="text-lg font-semibold">{{ $post['user']['name'] }}</p>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Media Section (Images/Video) -->
        <div class="relative">



            @if(count($post['media']) > 0)
                @php
                    $media = $post['media'][0];
                @endphp

                @if(Str::contains($media['mime_type'], 'video'))
                    <video controls class="object-cover w-full h-80">
                        <source src="{{ $media['original_url'] }}" type="{{ $media['mime_type'] }}">
                        Your browser does not support the video tag.
                    </video>
                @elseif(Str::contains($media['mime_type'], 'image'))
                    <img src="{{ $media['original_url'] }}" class="object-cover w-full h-80" alt="Post Image">
                @endif
            @else
                <img src="{{ asset('images/default-image.jpg') }}" class="object-cover w-full h-80" alt="No Media">
            @endif
        </div>

        <!-- Admin Controls for Status and Visibility -->
        <div class="mt-4">
            <div class="flex justify-between mb-4">
                <div>
                    <label for="status" class="block text-sm text-gray-700">Status</label>
                    <select id="status" name="status" class="form-select">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $status == $post['status'] ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="visibility" class="block text-sm text-gray-700">Visibility</label>
                    <select id="visibility" name="visibility" class="form-select">
                        @foreach($visibilities as $visibility)
                            <option value="{{ $visibility }}" {{ $visibility == $post['visibility'] ? 'selected' : '' }}>{{ ucfirst($visibility) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Like and Comment Buttons (Optional) -->
        {{-- <div class="flex items-center justify-between">
            <a href="{{ route('admin.post.likes', $post['id']) }}" class="flex items-center space-x-2 text-blue-500 hover:text-blue-700">
                <i class="fas fa-thumbs-up"></i>
                <span>{{ $likes_count }} Likes</span>
            </a>
            <a href="{{ route('admin.post.comments', $post['id']) }}" class="flex items-center space-x-2 text-blue-500 hover:text-blue-700">
                <i class="fas fa-comment-dots"></i>
                <span>{{ $comments_count }} Comments</span>
            </a>
        </div> --}}

        <!-- Likes Section -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold">Likes ({{ $post->likes_count }})</h3>
            @if(count($post['likes']) > 0)
                <ul class="mt-2 divide-y divide-gray-200">
                    @foreach ($post['likes'] as $like)
                        <li class="flex items-center gap-3 py-2">
                            <img src="{{ $like['profile_picture'] ?? asset('images/default-avatar.png') }}" alt="Profile Picture" class="w-8 h-8 rounded-full">
                            <span class="font-medium">{{ $like['username'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-gray-500">No likes yet.</p>
            @endif
        </div>

        <!-- Comments Section -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold">Comments ({{ $post->comments_count }})</h3>
            @if(count($post['comments']) > 0)
                @foreach ($post['comments'] as $comment)
                    <div class="flex gap-3 py-3">
                        <img src="{{ $comment['profile_picture'] ?? asset('images/default-avatar.png') }}" alt="Profile Picture" class="w-8 h-8 rounded-full">
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <span class="font-semibold">{{ $comment['username'] }}</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-700">{{ $comment['content'] }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="mt-2 text-sm text-gray-500">No comments yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
