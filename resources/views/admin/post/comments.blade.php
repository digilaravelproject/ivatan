@extends('admin.layouts.app')
@section('title', 'Post Comments')

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
            <h2 class="mb-4 text-xl font-bold">Comments ({{ $post->comments->count() }})</h2>

            @if ($post->comments->isEmpty())
                <p class="text-gray-400">No comments yet.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach ($post->comments->whereNull('parent_id') as $comment)
                        @include('admin.post.partials.comment', ['comment' => $comment, 'level' => 0])
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
