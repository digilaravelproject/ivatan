@extends('admin.layouts.app')
@section('title', 'Ad Packages')

@section('content')
    <div class="mx-auto space-y-6 max-w-7xl">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Ad Packages</h2>
            <a href="{{ route('admin.ad.ad-packages.create') }}"
                class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                + Create Package
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed z-50 w-full max-w-sm p-4 text-white bg-green-500 rounded shadow-lg top-4 right-4">
                <div class="flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-lg font-bold">&times;</button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed z-50 w-full max-w-sm p-4 text-white bg-red-500 rounded shadow-lg top-4 right-4">
                <div class="flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-lg font-bold">&times;</button>
                </div>
            </div>
        @endif

        {{-- Sorting Helper --}}
        @php
            function sortUrl($column)
            {
                $currentSort = request('sort_by');
                $currentDir = request('direction', 'asc');
                $isActive = $currentSort === $column;
                $newDir = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';

                return request()->fullUrlWithQuery([
                    'sort_by' => $column,
                    'direction' => $newDir,
                ]);
            }

            function sortArrow($column)
            {
                $currentSort = request('sort_by');
                $currentDir = request('direction', 'asc');

                if ($currentSort === $column) {
                    return $currentDir === 'asc' ? '↑' : '↓';
                }

                return '';
            }
        @endphp

        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">
                            <a href="{{ sortUrl('id') }}" class="hover:underline"># {{ sortArrow('id') }}</a>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">
                            <a href="{{ sortUrl('name') }}" class="hover:underline">Title {{ sortArrow('name') }}</a>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">
                            <a href="{{ sortUrl('price') }}" class="hover:underline">Price {{ sortArrow('price') }}</a>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">
                            <a href="{{ sortUrl('duration_days') }}" class="hover:underline">Duration
                                {{ sortArrow('duration_days') }}</a>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">
                            <a href="{{ sortUrl('reach_limit') }}" class="hover:underline">Reach Limit
                                {{ sortArrow('reach_limit') }}</a>
                        </th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Targeting</th>
                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($packages as $p)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $p->id }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">₹{{ number_format($p->price, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $p->duration_days }} days</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $p->reach_limit }}</td>
                            <td class="px-4 py-3 text-sm">
                                <pre class="text-xs text-gray-500 whitespace-pre-wrap">{{ json_encode($p->targeting, JSON_PRETTY_PRINT) }}</pre>
                            </td>
                            <td class="px-4 py-3 space-x-2 text-right">
                                {{-- Edit --}}
                                <a href="{{ route('admin.ad.ad-packages.edit', $p) }}"
                                    class="inline-block px-3 py-1 text-sm text-yellow-700 transition rounded bg-yellow-50 hover:bg-yellow-100">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <button x-data
                                    x-on:click="$dispatch('open-delete-modal', { id: {{ $p->id }}, title: '{{ e($p->name) }}' })"
                                    class="px-3 py-1 text-sm text-red-700 transition rounded bg-red-50 hover:bg-red-100">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No packages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $packages->links() }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ modal: null }" x-on:open-delete-modal.window="modal = $event.detail">
        <template x-if="modal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div class="w-full max-w-md px-5 py-5 bg-white rounded-lg shadow-lg">
                    <h3 class="mb-3 text-lg font-semibold text-red-600">Delete Ad Package</h3>
                    <p class="mb-4 text-sm text-gray-600">
                        Are you sure you want to delete:
                        <strong x-text="modal.title"></strong>?
                    </p>
                    <form :action="`/admin/ad/ad-packages/${modal.id}`" method="POST" class="space-y-4">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end gap-2 py-2">
                            <button type="button" @click="modal = null"
                                class="px-4 py-2 transition border rounded hover:bg-gray-100">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-white transition bg-red-600 rounded hover:bg-red-700">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
