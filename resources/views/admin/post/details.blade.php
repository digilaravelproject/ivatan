{{-- resources/views/admin/post/details.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Post Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Post Details Section -->
    <div class="bg-white shadow rounded-lg p-6">
        <!-- Profile Section -->
        <div class="flex items-center mb-4">
            <img src="{{ $postDetails['user']['profile_picture'] ?? asset('images/default-avatar.png') }}"
                alt="Profile Picture" class="w-12 h-12 rounded-full object-cover">
            <div class="ml-3">
                <p class="font-semibold text-lg">{{ $postDetails['user']['name'] }}</p>
                <p class="text-xs text-gray-500">{{ $postDetails['created_at']->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Media Section (Images/Video) -->
        <div class="relative">
            <!-- Carousel for images -->
            @if(count($postDetails['media_metadata']['images']) > 1)
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($postDetails['media_metadata']['images'] as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-full" alt="Post Image">
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </a>
                </div>
            @elseif(count($postDetails['media_metadata']['images']) === 1)
                <img src="{{ asset('storage/' . $postDetails['media_metadata']['images'][0]) }}"
                    class="w-full h-80 object-cover" alt="Post Image">
            @elseif(count($postDetails['media_metadata']['videos']) > 0)
                <video controls class="w-full h-80 object-cover">
                    <source src="{{ asset('storage/' . $postDetails['media_metadata']['videos'][0]) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                <img src="{{ asset('images/default-image.jpg') }}" class="w-full h-80 object-cover" alt="No Media">
            @endif
        </div>

        <!-- Admin Controls for Status and Visibility -->
        <div class="mt-4">
            <div class="mb-4 flex justify-between">
                <div>
                    <label for="status" class="block text-sm text-gray-700">Status</label>
                    <select id="status" name="status" class="form-select">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $status == $postDetails['status'] ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="visibility" class="block text-sm text-gray-700">Visibility</label>
                    <select id="visibility" name="visibility" class="form-select">
                        @foreach($visibilities as $visibility)
                            <option value="{{ $visibility }}" {{ $visibility == $postDetails['visibility'] ? 'selected' : '' }}>{{ ucfirst($visibility) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Like and Comment Buttons -->
        <div class="flex justify-between items-center">
            {{-- <a href="{{ route('admin.post.likes', $postDetails['post_id']) }}" class="text-blue-500 hover:text-blue-700 flex items-center space-x-2">
                <i class="fas fa-thumbs-up"></i>
                <span>{{ $postDetails['total_likes'] }} Likes</span>
            </a>
            <a href="{{ route('admin.post.comments', $postDetails['post_id']) }}" class="text-blue-500 hover:text-blue-700 flex items-center space-x-2">
                <i class="fas fa-comment-dots"></i>
                <span>{{ $postDetails['total_comments'] }} Comments</span>
            </a> --}}
        </div>

        <!-- Likes Section -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold">Likes ({{ $postDetails['total_likes'] }})</h3>
            <ul class="divide-y divide-gray-200 mt-2">
                @foreach ($postDetails['likes'] as $like)
                    <li class="flex items-center gap-3 py-2">
                        <img src="{{ $like['profile_picture'] }}" alt="Profile Picture" class="w-8 h-8 rounded-full">
                        <span class="font-medium">{{ $like['username'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Comments Section -->
        <div class="mt-6">
            <h3 class="text-xl font-semibold">Comments ({{ $postDetails['total_comments'] }})</h3>
            @foreach ($postDetails['comments'] as $comment)
                <div class="flex gap-3 py-3">
                    <img src="{{ $comment['profile_picture'] }}" alt="Profile Picture" class="w-8 h-8 rounded-full">
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <span class="font-semibold">{{ $comment['username'] }}</span>
                            <span class="text-xs text-gray-400">{{ $comment['created_at']->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ $comment['content'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
