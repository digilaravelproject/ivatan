@extends('admin.layouts.app')
@section('title', "Comment History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Comment History: {{ $user->name }}</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">On Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($comments as $comment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->id }}</td>
                    <td class="px-6 py-4 text-sm">{{ Str::limit($comment->body, 60) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ class_basename($comment->commentable_type) }} #{{ $comment->commentable_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">No comments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $comments->links() }}</div>
</div>
@endsection
