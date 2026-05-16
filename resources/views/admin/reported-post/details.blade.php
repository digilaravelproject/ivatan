@extends('admin.layouts.app')
@section('title', 'Reported Post Details')

@section('content')
    <div class="max-w-2xl px-4 py-1 mx-auto space-y-10 sm:px-6 lg:px-8">

        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('admin.reported-post.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                ← Back
            </a>
        </div>

        {{-- Card --}}
        <div class="p-4 bg-white shadow rounded-xl">

            {{-- User Info --}}
            <div class="flex items-center mb-6 space-x-4">
                <img
                    src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                    class="object-cover border rounded-full w-14 h-14"
                >
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $post->user->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $post->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            @php
                $postType = $post->type;

                $image = $post->getMedia('images')->first();
                $video = $post->getMedia('videos')->first();

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
                        <video controls
                               class="absolute inset-0 object-contain w-full h-full">
                            <source src="{{ $video->getUrl() }}" type="video/mp4">
                        </video>
                    @elseif ($image)
                        <img src="{{ $image->getUrl() }}"
                             class="absolute inset-0 object-contain w-full h-full">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                            No media available
                        </div>
                    @endif
                </div>
            </div>

            {{-- Caption --}}
            <p class="text-base leading-relaxed text-gray-800">
                {{ $post->caption }}
            </p>

            {{-- Stats --}}
            <div class="flex gap-6 pt-4 text-sm text-gray-700">
                <span>👍 {{ $post->likes_count }}</span>
                <span>💬 {{ $post->comments_count }}</span>
                <span class="text-red-600">🚩 {{ $post->reports_count }} Reports</span>
            </div>

            {{-- Reports --}}
            <div class="pt-6 mt-8 border-t">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">
                    Report Details
                </h3>

                <div class="space-y-4">
                    @foreach ($post->reports as $report)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-800">
                                    {{ $report->user->name }}
                                </p>
                                <span class="text-xs text-gray-500">
                                    {{ $report->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-red-600 font-semibold">
                                Reason: {{ $report->reason }}
                            </p>

                            @if ($report->description)
                                <p class="mt-2 text-sm text-gray-700">
                                    {{ $report->description }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Admin Actions --}}
            <div class="flex items-center justify-between pt-6 mt-10 border-t border-gray-200">

                {{-- Delete --}}
                <form action="{{ route('admin.reported-post.delete', $post->id) }}"
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this reported post?');">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md shadow">
                        Delete Post
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection
