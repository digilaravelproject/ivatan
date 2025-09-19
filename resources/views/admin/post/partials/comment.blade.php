@php
    $indent = $level * 20; // Indent replies
@endphp

<li class="flex gap-3 py-4" style="margin-left: {{ $indent }}px;">
    <img src="{{ $comment->user->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="User Avatar"
        class="object-cover w-10 h-10 border rounded-full">

    <div class="flex-1">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.show', $comment->user) }}"
                class="text-sm font-semibold text-gray-800 hover:underline">
                {{ $comment->user->name }}
            </a>
            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
        </div>

        <p class="mt-1 text-sm text-gray-700">{{ $comment->body }}</p>

        @php $likeCount = $comment->likes()->count(); @endphp
        @if ($likeCount > 0)
            <p class="mt-1 text-xs text-gray-500">{{ $likeCount }} {{ Str::plural('Like', $likeCount) }}</p>
        @endif

        {{-- Delete Button --}}
        <form action="{{ route('admin.userpost.comments.delete', $comment->id) }}" method="POST" class="mt-2"
            onsubmit="return confirm('Are you sure you want to delete this comment?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-red-600 hover:underline">Delete Comment</button>
        </form>
    </div>
</li>

{{-- Recursive replies --}}
@if ($comment->replies->count() > 0)
    @foreach ($comment->replies as $reply)
        @include('admin.post.partials.comment', ['comment' => $reply, 'level' => $level + 1])
    @endforeach
@endif
