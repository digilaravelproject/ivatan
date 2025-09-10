@extends('admin.layouts.app')
@section('title', 'Post Likes')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Likes ({{ $post->likes->count() }})</h2>

        @if($post->likes->isEmpty())
            <p class="text-gray-400">No likes yet.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($post->likes as $like)
                    <li class="flex items-center gap-3 py-2">
                        <img src="{{ $like->user->profile_photo_url  ?? asset('images/default-avatar.png') }}"
                             class="w-8 h-8 rounded-full">
                        <span> <a href="{{ route('admin.users.show', $like->user) }}">{{ $like->user->name }}</a></span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
