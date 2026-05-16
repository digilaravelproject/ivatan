@php
    $type = request()->get('type', 'post');
    $labels = [
        'post' => 'Posts',
        'video' => 'Videos',
        'reel' => 'Reels',
    ];
    $pageTitle = 'Reported ' . ($labels[$type] ?? 'Posts');
@endphp

@extends('admin.layouts.app')
@section('title', $pageTitle)

@section('content')
<div class="p-6 mx-auto space-y-8 bg-white rounded-lg shadow max-w-7xl">

    {{-- Navigation Tabs --}}
    <div class="flex justify-around mb-6 border-b">
        @foreach ($labels as $key => $label)
            <a href="{{ route('admin.reported-post.index', ['type' => $key]) }}"
               class="{{ $type === $key ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-red-500' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <h2 class="mb-4 text-2xl font-bold">{{ $pageTitle }}</h2>

    @if ($posts->count())
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <div class="overflow-hidden bg-white border rounded-lg shadow-sm border-red-200">

                    {{-- Header --}}
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center">
                            <img
                                src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                class="w-10 h-10 rounded-full"
                            >
                            <div class="ml-3">
                                <p class="text-sm font-semibold">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Reports Count --}}
                            <span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">
                                {{ $post->reports_count }} Reports
                            </span>
                        </div>
                    </div>

                    {{-- Media --}}
                    <a href="{{ route('admin.reported-post.details', $post->id) }}">
                        <div class="relative w-full aspect-square">
                            @foreach ($post->media as $media)
                                @include('components.admin.media-render', ['media' => $media])
                            @endforeach
                        </div>
                    </a>

                    {{-- Caption --}}
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-700 line-clamp-2">
                            {{ $post->caption }}
                        </p>
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-between px-4 py-2 text-sm border-t">
                        <span>👍 {{ $post->likes_count }}</span>
                        <span>💬 {{ $post->comments_count }}</span>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $posts->links('pagination::tailwind') }}
        </div>
    @else
        <div class="py-12 text-center text-gray-500">
            No reported posts found.
        </div>
    @endif
</div>
@endsection
