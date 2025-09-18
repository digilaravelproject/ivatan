@extends('admin.layouts.app')
@section('title', 'Pending Ads')

@section('content')
    <div class="py-6 mx-auto space-y-6 max-w-7xl">

        {{-- Header --}}
        <h2 class="text-2xl font-semibold">Pending Ads</h2>

        {{-- Flash messages --}}
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
                class="fixed z-50 w-full max-w-sm p-4 text-white bg-red-600 rounded shadow-lg top-4 right-4">
                <div class="flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-lg font-bold">&times;</button>
                </div>
            </div>
        @endif

        {{-- Ads Table --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">#</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">User</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Package</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Title</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Submitted At</th>
                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($ads as $ad)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->user->name ?? 'Guest' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->package->title ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->title }}</td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $status = strtolower($ad->status);
                                    $badgeClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $ad->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $ad->created_at->format('d M, Y H:i') }}</td>
                            <td class="px-4 py-3 space-x-2 text-right">

                                {{-- Approve --}}
                                @if ($ad->status !== 'approved')
                                    <form method="POST" action="{{ route('admin.ads.approve', $ad) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm text-green-700 transition rounded bg-green-50 hover:bg-green-100"
                                            onclick="return confirm('Approve this ad?')">Approve</button>
                                    </form>
                                @endif

                                {{-- Reject --}}
                                @if ($ad->status !== 'rejected')
                                    <button x-data
                                        x-on:click="$dispatch('open-reject-modal', { id: {{ $ad->id }}, title: '{{ addslashes($ad->title) }}' })"
                                        class="px-3 py-1 text-sm text-red-700 transition rounded bg-red-50 hover:bg-red-100">
                                        Reject
                                    </button>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No pending ads found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $ads->links() }}
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-data="{ modal: null }" x-on:open-reject-modal.window="modal = $event.detail">
        <template x-if="modal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
                <div class="w-full px-5 py-5 bg-white rounded-lg shadow-lg" style="max-width: 50%;">

                    <h3 class="mb-3 text-lg font-semibold">Reject Ad</h3>
                    <p class="mb-4 text-sm text-gray-600">Rejecting: <strong x-text="modal.title"></strong></p>

                    <form :action="`/admin/ads/${modal.id}/reject`" method="POST" class="space-y-4">
                        @csrf
                        <textarea name="admin_note" rows="4" class="w-full p-2 border rounded"
                            placeholder="Reason for rejection (optional)"></textarea>
                        <div class="flex justify-end gap-2 py-2">
                            <button type="button" @click="modal = null"
                                class="px-4 py-2 transition border rounded hover:bg-gray-100">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-white transition bg-red-600 rounded hover:bg-red-700">Reject</button>
                        </div>
                    </form>

                </div>
            </div>
        </template>
    </div>
@endsection
