@extends('admin.layouts.app')
@section('title', "Like History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Like History: {{ $user->name }}</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($likes as $like)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $like->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $like->likeable_type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $like->likeable_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $like->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">No likes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $likes->links() }}</div>
</div>
@endsection
