@extends('admin.layouts.app')

@section('title', 'Manage Interests')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Create Form --}}
        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-lg font-semibold">Add New Interest</h2>
            <form action="{{ route('admin.interests.store') }}" method="POST" class="flex gap-4">
                @csrf
                <input type="text" name="name" placeholder="Enter interest (e.g. Coding, Music)"
                    class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
                <button type="submit" class="px-6 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Add</button>
            </form>
            @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        {{-- List Table --}}
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Users Count</th>
                        <th class="px-6 py-3 text-xs font-medium text-right text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($interests as $interest)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $interest->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $interest->users()->count() }} Users</td>
                            <td class="px-6 py-4 text-sm font-medium text-right">
                                <form action="{{ route('admin.interests.destroy', $interest) }}" method="POST"
                                    onsubmit="return confirm('Delete this interest?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No interests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $interests->links() }}
            </div>
        </div>
    </div>
@endsection