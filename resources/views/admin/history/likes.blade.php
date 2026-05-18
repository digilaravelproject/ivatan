@extends('admin.layouts.app')
@section('title', "Like History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Like History: {{ $user->name }}</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liked Content</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caption</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($likes as $like)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($like->likeable && $like->likeable instanceof \App\Models\UserPost)
                            <div class="flex items-center gap-3">
                                @if($like->likeable->getFirstMediaUrl('default'))
                                    <img src="{{ $like->likeable->getFirstMediaUrl('default') }}" alt="" class="w-10 h-10 rounded object-cover">
                                @endif
                                <div>
                                    <div class="font-medium">{{ class_basename($like->likeable_type) }}</div>
                                    <div class="text-xs text-gray-400">#{{ $like->likeable_id }}</div>
                                </div>
                            </div>
                        @else
                            {{ class_basename($like->likeable_type) }} #{{ $like->likeable_id }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ $like->likeable?->caption ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $like->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">No likes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $likes->links() }}</div>
</div>
@endsection
