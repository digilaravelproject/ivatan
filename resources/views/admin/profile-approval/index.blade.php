@extends('admin.layouts.app')
@section('title', 'Profile Approvals')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">👤 Profile Switch Approvals</h1>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Approved Today</p>
        <p class="text-2xl font-bold text-green-600">{{ $summary['approved_today'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Rejected Today</p>
        <p class="text-2xl font-bold text-red-600">{{ $summary['rejected_today'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Total Requests</p>
        <p class="text-2xl font-bold">{{ $summary['total'] ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border rounded px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="border rounded px-3 py-2 text-sm w-48">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.profile-approval.index') }}" class="text-gray-500 px-3 py-2 text-sm">Clear</a>
        </form>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">From</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">To</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Requested</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($requests as $req)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium">{{ $req->user?->name }}</div>
                    <div class="text-xs text-gray-500">{{ $req->user?->email }}</div>
                </td>
                <td class="px-4 py-3 text-sm capitalize">{{ $req->fromProfile?->type ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-sm capitalize">
                    <span class="font-medium">{{ $req->to_profile_type }}</span>
                    @if($req->toProfile?->sellerDetails?->seller_type)
                    <span class="text-xs text-gray-500">({{ $req->toProfile->sellerDetails->seller_type }})</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full {{ $req->status === 'approved' ? 'bg-green-100 text-green-800' : ($req->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm">{{ $req->created_at->diffForHumans() }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.profile-approval.show', $req->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">Review</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No profile switch requests found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t">{{ $requests->links() }}</div>
</div>
@endsection
