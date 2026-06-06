@extends('admin.layouts.app')
@section('title', 'All Subscriptions')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">📋 All Subscriptions</h1>
</div>

<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Total</p>
        <p class="text-2xl font-bold">{{ $summary['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-green-500 text-sm">Active</p>
        <p class="text-2xl font-bold text-green-600">{{ $summary['active'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-red-500 text-sm">Cancelled</p>
        <p class="text-2xl font-bold text-red-600">{{ $summary['cancelled'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Expired</p>
        <p class="text-2xl font-bold">{{ $summary['expired'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-blue-500 text-sm">Monthly Revenue</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['monthly_revenue'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border rounded px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="past_due" @selected(request('status') === 'past_due')>Past Due</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Plan</label>
                <select name="plan_id" class="border rounded px-3 py-2 text-sm">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(request('plan_id') == $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search User</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="border rounded px-3 py-2 text-sm w-48">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-500 px-3 py-2 text-sm hover:text-gray-700">Clear</a>
        </form>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Plan</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Profile</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Started</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Next Billing</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($subscriptions as $sub)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium">{{ $sub->user?->name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $sub->user?->email }}</div>
                </td>
                <td class="px-4 py-3 text-sm">{{ $sub->plan?->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-sm capitalize">{{ $sub->profile?->type ?? 'N/A' }}</td>
                <td class="px-4 py-3">
                    @php
                    $statusColors = ['active' => 'bg-green-100 text-green-800', 'past_due' => 'bg-yellow-100 text-yellow-800', 'cancelled' => 'bg-red-100 text-red-800', 'expired' => 'bg-gray-100 text-gray-800', 'pending' => 'bg-blue-100 text-blue-800'];
                    $color = $statusColors[$sub->status] ?? 'bg-gray-100';
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $color }}">{{ ucfirst($sub->status) }}</span>
                </td>
                <td class="px-4 py-3 text-sm">{{ $sub->starts_at?->format('d M Y') }}</td>
                <td class="px-4 py-3 text-sm">{{ $sub->next_billing_at?->format('d M Y') ?? ($sub->ends_at?->format('d M Y') ?? '—') }}</td>
                <td class="px-4 py-3 text-sm font-medium">{{ number_format($sub->plan?->price ?? 0, 2) }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">No subscriptions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
