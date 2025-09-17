@extends('admin.layouts.app')
@section('title', 'All Products')

@section('content')

<div class="mx-auto space-y-6 max-w-7xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">User Products</h2>

        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search title / slug / uuid"
                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring" />

            <select name="status" class="py-2 border rounded-lg ">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
            </select>

            <button type="submit" class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">Filter</button>
        </form>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed z-50 w-full max-w-sm p-4 text-white bg-green-500 rounded shadow-lg top-4 right-4">
            <div class="flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-lg font-bold">&times;</button>
            </div>
        </div>
    @endif

    {{-- Product Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">#</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Thumbnail</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Product</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Seller</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Price</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Status</th>
                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Created</th>
                    <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($services as $p)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $p->id }}</td>

                        {{-- Thumbnail --}}
                        <td class="px-4 py-3">
                            @if($p->cover_image)
                                <img src="{{ asset('storage/' . $p->cover_image) }}" alt="Product Image"
                                     class="object-cover w-12 h-12 rounded">
                            @else
                                <div class="flex items-center justify-center w-12 h-12 text-xs text-gray-400 bg-gray-100 rounded">No Img</div>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $p->title }}</div>
                            <div class="text-xs text-gray-500">{{ $p->slug }}</div>
                            <div class="text-xs text-gray-400">UUID: {{ $p->uuid }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <div>{{ optional($p->seller)->name }}</div>
                            <div class="text-xs text-gray-500">{{ optional($p->seller)->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">₹ {{ number_format($p->price, 2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if ($p->status === 'pending')
                                <span class="px-2 py-1 text-xs text-yellow-700 bg-yellow-100 rounded">Pending</span>
                            @elseif($p->status === 'approved')
                                <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs text-red-700 bg-red-100 rounded">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $p->created_at->format('d M, Y') }}</td>
                        <td class="px-4 py-3 space-x-2 text-right">

                            {{-- View --}}
                            <a href="{{ route('admin.services.show', $p) }}"
                               class="inline-block px-3 py-1 text-sm text-blue-700 transition rounded bg-blue-50 hover:bg-blue-100">View</a>

                            {{-- Approve --}}
                            @if ($p->status !== 'approved')
                                <form method="POST" action="{{ route('admin.services.approve', $p) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 text-sm text-green-700 transition rounded bg-green-50 hover:bg-green-100"
                                        onclick="return confirm('Approve this product?')">Approve</button>
                                </form>
                            @endif

                            {{-- Reject --}}
                            @if ($p->status !== 'rejected')
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-reject-modal', { id: {{ $p->id }}, title: '{{ addslashes($p->title) }}' })"
                                    class="px-3 py-1 text-sm text-red-700 transition rounded bg-red-50 hover:bg-red-100">Reject</button>
                            @endif

                            {{-- Admin note --}}
                            @if ($p->admin_note)
                                <div class="mt-1 text-xs text-gray-500">
                                    <strong>Note:</strong> {{ \Illuminate\Support\Str::limit($p->admin_note, 80) }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $services->links() }}
    </div>
</div>

{{-- Reject Modal --}}
<div x-data="{ modal: null }" x-on:open-reject-modal.window="modal = $event.detail">
    <template x-if="modal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="w-full px-5 py-5 bg-white rounded-lg shadow-lg" style="max-width: 50%;">


                <h3 class="mb-3 text-lg font-semibold">Reject Product</h3>
                <p class="mb-4 text-sm text-gray-600">Rejecting: <strong x-text="modal.title"></strong></p>

                <form :action="`/admin/services/${modal.id}/reject`" method="POST" class="space-y-4">
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
