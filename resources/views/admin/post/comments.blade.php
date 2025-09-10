@extends('admin.layouts.app')
@section('title', 'Post Comments')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Comments ({{ $post->comments->count() }})</h2>

        @if($post->comments->isEmpty())
            <p class="text-gray-400">No comments yet.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($post->comments as $comment)
                    <li class="flex gap-3 py-3">
                        <img src="{{ $comment->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                             class="w-8 h-8 rounded-full">
                        <div class="flex-1">
                            <div class="flex justify-between">

                                <span class="font-semibold">
                                    <a href="{{ route('admin.users.show', $comment->user) }}">{{ $comment->user->name }}</a></span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                            <p class="text-xs text-gray-500">{{ $comment->likes()->count() }} Likes</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
