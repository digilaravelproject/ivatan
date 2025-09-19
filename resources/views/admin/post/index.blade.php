@php
    $type = request()->get('type', 'post');
    $labels = [
        'post' => 'Posts',
        'video' => 'Videos',
        'reel' => 'Reels',
    ];
    $pageTitle = $labels[$type] ?? 'User Content';
@endphp

@extends('admin.layouts.app')
@section('title', $pageTitle)

@section('content')
    <div class="p-6 mx-auto space-y-8 bg-white rounded-lg shadow max-w-7xl">

        {{-- Navigation Tabs --}}
        <div class="flex justify-around mb-6 border-b">
            @foreach ($labels as $key => $label)
                <a href="{{ route('admin.userpost.index', ['type' => $key]) }}"
                    class="{{ $type === $key ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <h2 class="mb-4 text-2xl font-bold capitalize">{{ $pageTitle }}</h2>

        <div class="p-6">
            @if ($posts->count())
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                            {{-- Post Header --}}
                            <div class="flex items-center p-4">
                                <img src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                    alt="user profile" class="object-cover w-10 h-10 rounded-full">
                                <div class="ml-3">
                                    <a href="{{ route('admin.users.show', $post->user->id) }}"
                                        class="text-sm font-semibold text-gray-800">
                                        {{ $post->user->name }}
                                    </a>
                                    <p class="text-xs text-gray-500">
                                        {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            {{-- Media --}}
                            <a href="{{ url("admin/user-posts/{$post->id}") }}">
                                <div
                                    class="relative w-full {{ $post->type === 'reel' ? 'aspect-[9/16]' : 'aspect-square' }}">
                                    @if ($post->type === 'carousel')
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                @foreach ($post['media'] as $media)
                                                    <div class="swiper-slide">
                                                        @include('components.admin.media-render', [
                                                            'media' => $media,
                                                        ])
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    @else
                                        @foreach ($post['media'] as $media)
                                            @include('components.admin.media-render', ['media' => $media])
                                        @endforeach
                                    @endif
                                </div>
                            </a>

                            {{-- Caption --}}
                            <div class="px-4 py-2">
                                <p class="text-sm text-gray-700">{{ $post->caption }}</p>
                            </div>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between px-4 py-2 text-sm text-gray-600 border-t">
                                <a href="{{ route('admin.userpost.likes', $post->id) }}"
                                    class="flex items-center space-x-1 hover:text-blue-600">
                                    <i class="fas fa-thumbs-up"></i>
                                    <span>{{ $post->likes_count }} Likes</span>
                                </a>
                                <a href="{{ route('admin.userpost.comments', $post->id) }}"
                                    class="flex items-center space-x-1 hover:text-blue-600">
                                    <i class="fas fa-comment-dots"></i>
                                    <span>{{ $post->comments_count }} Comments</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $posts->links('pagination::tailwind') }}
                </div>
            @else
                <div class="py-12 text-center text-gray-500">No {{ $pageTitle }} available.</div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('.swiper-container').forEach(el => {
                new Swiper(el, {
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    }
                });
            });
        });
    </script>
@endsection
