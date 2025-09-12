@extends('admin.layouts.app')
@section('title', 'All Products')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">User Products</h2>

        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search title / slug / uuid"
                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring" />

            <select name="status" class=" py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Filter</button>
        </form>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white p-4 rounded shadow-lg max-w-sm w-full">
            <div class="flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-lg font-bold">&times;</button>
            </div>
        </div>
    @endif

    {{-- Product Table --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Thumbnail</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Seller</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Created</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($products as $p)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $p->id }}</td>

                        {{-- Thumbnail --}}
                        <td class="px-4 py-3">
                            @if($p->cover_image)
                                <img src="{{ asset('storage/' . $p->cover_image) }}" alt="Product Image"
                                     class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-100 flex items-center justify-center rounded text-gray-400 text-xs">No Img</div>
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
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Pending</span>
                            @elseif($p->status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Approved</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $p->created_at->format('d M, Y') }}</td>
                        <td class="px-4 py-3 text-right space-x-2">

                            {{-- View --}}
                            <a href="{{ route('admin.products.show', $p) }}"
                               class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded text-sm hover:bg-blue-100 transition">View</a>

                            {{-- Approve --}}
                            @if ($p->status !== 'approved')
                                <form method="POST" action="{{ route('admin.products.approve', $p) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 bg-green-50 text-green-700 rounded text-sm hover:bg-green-100 transition"
                                        onclick="return confirm('Approve this product?')">Approve</button>
                                </form>
                            @endif

                            {{-- Reject --}}
                            @if ($p->status !== 'rejected')
                                <button
                                    x-data
                                    x-on:click="$dispatch('open-reject-modal', { id: {{ $p->id }}, title: '{{ addslashes($p->title) }}' })"
                                    class="px-3 py-1 bg-red-50 text-red-700 rounded text-sm hover:bg-red-100 transition">Reject</button>
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
        {{ $products->links() }}
    </div>
</div>

{{-- Reject Modal --}}
<div x-data="{ modal: null }" x-on:open-reject-modal.window="modal = $event.detail">
    <template x-if="modal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-lg shadow-lg w-full py-5 px-5" style="max-width: 50%;">


                <h3 class="text-lg font-semibold mb-3">Reject Product</h3>
                <p class="text-sm text-gray-600 mb-4">Rejecting: <strong x-text="modal.title"></strong></p>

                <form :action="`/admin/products/${modal.id}/reject`" method="POST" class="space-y-4">
                    @csrf
                    <textarea name="admin_note" rows="4" class="w-full border rounded p-2"
                        placeholder="Reason for rejection (optional)"></textarea>
                    <div class="flex justify-end gap-2 py-2">
                        <button type="button" @click="modal = null"
                            class="px-4 py-2 border rounded hover:bg-gray-100 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>


@endsection
