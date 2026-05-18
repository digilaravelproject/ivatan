@extends('admin.layouts.app')
@section('title', "Video View History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Video View History: {{ $user->name }}</h2>
    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.users.history.video-views', ['user' => $user, 'filter' => 'reels']) }}" class="px-3 py-1 rounded bg-blue-100 text-blue-700 text-sm">Reels</a>
        <a href="{{ route('admin.users.history.video-views', ['user' => $user, 'filter' => 'long_video']) }}" class="px-3 py-1 rounded bg-green-100 text-green-700 text-sm">Long Videos</a>
        <a href="{{ route('admin.users.history.video-views', ['user' => $user, 'filter' => 'both']) }}" class="px-3 py-1 rounded bg-gray-100 text-gray-700 text-sm">Both</a>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caption</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($views as $view)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($view->viewable)
                            <div class="flex items-center gap-3">
                                @if($view->viewable->getFirstMediaUrl('default'))
                                    <img src="{{ $view->viewable->getFirstMediaUrl('default') }}" alt="" class="w-10 h-10 rounded object-cover">
                                @endif
                                <div class="text-xs text-gray-400">#{{ $view->viewable_id }}</div>
                            </div>
                        @else
                            #{{ $view->viewable_id }}
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ $view->post_caption ?? ($view->viewable->caption ?? '—') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $view->post_type === 'reel' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $view->post_type ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $view->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">No views found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $views->links() }}</div>
</div>
@endsection
