@extends('admin.layouts.app')
@section('title', "Comment History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Comment History: {{ $user->name }}</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">On Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($comments as $comment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($comment->commentable && $comment->commentable instanceof \App\Models\UserPost)
                            <div class="flex items-center gap-3">
                                @if($comment->commentable->getFirstMediaUrl('default'))
                                    <img src="{{ $comment->commentable->getFirstMediaUrl('default') }}" alt="" class="w-10 h-10 rounded object-cover">
                                @endif
                                <div class="max-w-xs">
                                    <div class="text-xs text-gray-400">#{{ $comment->commentable_id }}</div>
                                    <div class="text-sm text-gray-600 truncate">{{ $comment->commentable->caption ?? '—' }}</div>
                                </div>
                            </div>
                        @else
                            {{ class_basename($comment->commentable_type) }} #{{ $comment->commentable_id }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm max-w-xs">{{ Str::limit($comment->body, 80) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">No comments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $comments->links() }}</div>
</div>
@endsection
