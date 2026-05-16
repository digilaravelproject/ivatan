@extends('admin.layouts.app')

@section('title', 'All Ads')

@section('content')

    <div class="py-6 mx-auto space-y-6 max-w-7xl">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">All Ads</h2>

            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title / user / package"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:ring" />

                <select name="status" class="py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                    <option value="awaiting_payment" @selected(request('status') == 'awaiting_payment')>Awaiting Payment</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">Filter</button>
            </form>
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

        {{-- Ads Table --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">#</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Title</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">User</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Package</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Status</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Impressions</th>
                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500">Submitted</th>
                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($ads as $ad)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $ad->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $ad->user->name ?? 'Guest' }}<br>
                                <span class="text-xs text-gray-500">{{ $ad->user->email ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $ad->package->name ?? '-' }}<br>
                                <span class="text-xs text-gray-500">
                                    ₹{{ number_format($ad->package->price ?? 0, 2) }} {{ $ad->package->currency ?? '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $status = strtolower($ad->status);
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'awaiting_payment' => 'bg-blue-100 text-blue-700',
                                    ];
                                    $badgeClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $ad->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $ad->impressions }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $ad->created_at->format('d M, Y H:i') }}</td>
                            <td class="px-4 py-3 space-x-2 text-right">
                                {{-- View --}}
                                <a href="{{ route('admin.ads.show', $ad) }}"
                                    class="inline-block px-3 py-1 text-sm text-blue-700 transition rounded bg-blue-50 hover:bg-blue-100">View</a>

                                @php
                                    $canApprove = in_array($ad->status, ['draft', 'pending_admin_approval']);
                                    $canReject = $ad->status !== 'rejected';
                                @endphp

                                {{-- Approve --}}
                                @if ($canApprove)
                                    <form method="POST" action="{{ route('admin.ads.approve', $ad) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm text-green-700 transition rounded bg-green-50 hover:bg-green-100"
                                            onclick="return confirm('Approve this ad?')">Approve</button>
                                    </form>
                                @endif

                                {{-- Reject --}}
                                @if ($canReject)
                                    <form method="POST" action="{{ route('admin.ads.reject', $ad) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm text-red-700 transition rounded bg-red-50 hover:bg-red-100"
                                            onclick="return confirm('Reject this ad?')">Reject</button>
                                    </form>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No ads found.</td>
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

@endsection
