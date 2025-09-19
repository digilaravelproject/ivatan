@extends('admin.layouts.app')
@section('title', 'Post Likes')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                ← Back
            </a>
        </div>

        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-bold">Likes ({{ $post->likes->count() }})</h2>

            @if ($post->likes->isEmpty())
                <p class="text-gray-400">No likes yet.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($post->likes as $like)
                        <li class="flex items-center gap-3 py-2">
                            <img src="{{ $like->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                class="w-8 h-8 rounded-full" alt="User Avatar">
                            <a href="{{ route('admin.users.show', $like->user) }}"
                                class="hover:underline">{{ $like->user->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
