@extends('admin.layouts.app')
@section('title', 'Post Details')

@section('content')
    <div class="max-w-2xl px-4 py-1 mx-auto space-y-10 sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                ← Back
            </a>
        </div>

        {{-- Card --}}
        <div class="p-4 bg-white shadow rounded-xl">
            {{-- User Info --}}
            <div class="flex items-center mb-6 space-x-4">
                <img src="{{ $postDetails['profile_pic'] ?? asset('images/default-avatar.png') }}" alt="User Avatar"
                    class="object-cover border rounded-full w-14 h-14">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $postDetails['user']['name'] }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($postDetails['created_at'])->diffForHumans() }}</p>
                </div>
            </div>

            @php
                $postType = $postDetails['type'];
                $image = $postDetails['media_metadata']['images'][0] ?? null;
                $video = $postDetails['media_metadata']['videos'][0] ?? null;

                $aspectClass = match ($postType) {
                    'post', 'carousel' => 'aspect-[4/5]',
                    'video' => 'aspect-video',
                    'reel' => 'aspect-[9/16]',
                    default => 'aspect-square',
                };
            @endphp

            {{-- Media --}}
            <div class="mb-6">
                <div class="relative w-full overflow-hidden rounded-lg bg-gray-100 {{ $aspectClass }}"
                    style="max-height: 600px;">
                    @if ($video)
                        <video controls controlsList="nodownload" oncontextmenu="return false"
                            class="absolute inset-0 object-contain w-full h-full">
                            <source src="{{ $video['original_url'] }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @elseif ($image)
                        <img src="{{ $image['original_url'] }}" alt="Post Image"
                            class="absolute inset-0 object-contain w-full h-full">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400 bg-gray-200">
                            No media available
                        </div>
                    @endif
                </div>
            </div>

            {{-- Caption --}}
            <p class="text-base leading-relaxed text-gray-800">{{ $postDetails['caption'] }}</p>

            {{-- Admin Controls --}}
            <form method="POST" action="{{ route('admin.userpost.update', $postDetails['post_id']) }}"
                class="pt-6 mt-8 space-y-6 border-t border-gray-200">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Post Status</label>
                        <select id="status" name="status"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}"
                                    {{ $status === $postDetails['status'] ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Visibility --}}
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
                        <select id="visibility" name="visibility"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($visibilities as $visibility)
                                <option value="{{ $visibility }}"
                                    {{ $visibility === $postDetails['visibility'] ? 'selected' : '' }}>
                                    {{ ucfirst($visibility) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow">
                        Update Post
                    </button>

                    <form action="{{ route('admin.userpost.softDelete', $postDetails['post_id']) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to soft delete this post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="ml-4 text-sm inline-flex items-center px-5 py-2.5 font-medium bg-red-500 rounded-md shadow-sm text-white hover:underline hover:bg-red-700">
                            Delete Post
                        </button>
                    </form>
                </div>
            </form>

            {{-- Likes & Comments --}}
            <div class="flex justify-between pt-6 mt-10 text-base text-gray-700 border-t border-gray-200">
                <a href="{{ route('admin.userpost.likes', $postDetails['post_id']) }}"
                    class="hover:underline"><strong>Likes:</strong> {{ $postDetails['total_likes'] }}</a>
                <a href="{{ route('admin.userpost.comments', $postDetails['post_id']) }}"
                    class="hover:underline"><strong>Comments:</strong> {{ $postDetails['total_comments'] }}</a>
            </div>
        </div>
    </div>
@endsection
