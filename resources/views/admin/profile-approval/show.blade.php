@extends('admin.layouts.app')
@section('title', 'Review Profile Switch Request')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.profile-approval.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Requests</a>
        <h1 class="text-2xl font-bold mt-1">Review Profile Switch Request</h1>
    </div>
    <div class="flex gap-2">
        @if($switchRequest->status === 'pending')
        <form method="POST" action="{{ route('admin.profile-approval.approve', $switchRequest->id) }}" class="inline" onsubmit="return confirm('Approve this profile switch?')">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 font-medium">✅ Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.profile-approval.reject', $switchRequest->id) }}" class="inline" onsubmit="return confirm('Reject this profile switch?')">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 font-medium">❌ Reject</button>
        </form>
        @else
        <span class="px-4 py-2 text-sm bg-gray-100 rounded text-gray-600">Already {{ $switchRequest->status }}</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold text-lg mb-4">Request Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-gray-500 text-sm">Status</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full {{ $switchRequest->status === 'approved' ? 'bg-green-100 text-green-800' : ($switchRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($switchRequest->status) }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">From Profile</dt><dd class="font-medium capitalize">{{ $switchRequest->from_profile_type }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">To Profile</dt><dd class="font-medium capitalize">{{ $switchRequest->to_profile_type }}</dd></div>
            @if($switchRequest->to_seller_type)
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Seller Type</dt><dd class="font-medium capitalize">{{ $switchRequest->to_seller_type }}</dd></div>
            @endif
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Requested</dt><dd class="text-sm">{{ $switchRequest->created_at->format('d M Y H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Notes</dt><dd class="text-sm">{{ $switchRequest->notes ?? '—' }}</dd></div>
            @if($switchRequest->reviewed_by)
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Reviewed By</dt><dd class="text-sm">{{ $switchRequest->reviewer?->name ?? 'Admin' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Reviewed At</dt><dd class="text-sm">{{ $switchRequest->reviewed_at?->format('d M Y H:i') ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Admin Notes</dt><dd class="text-sm">{{ $switchRequest->admin_notes ?? '—' }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold text-lg mb-4">User Information</h2>
        <dl class="space-y-3">
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Name</dt><dd class="font-medium">{{ $switchRequest->user?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Email</dt><dd class="font-medium">{{ $switchRequest->user?->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500 text-sm">Joined</dt><dd class="text-sm">{{ $switchRequest->user?->created_at?->format('d M Y') ?? '—' }}</dd></div>
        </dl>

        <h3 class="font-semibold mt-6 mb-3">Current Profiles</h3>
        @if($switchRequest->user && $switchRequest->user->profiles)
        <div class="space-y-2">
            @foreach($switchRequest->user->profiles as $profile)
            <div class="flex items-center justify-between bg-gray-50 rounded px-3 py-2">
                <div>
                    <span class="text-sm font-medium capitalize">{{ $profile->profile_type }}</span>
                    @if($profile->is_active)
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full ml-2">Active</span>
                    @endif
                </div>
                <span class="text-xs text-gray-500">{{ $profile->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500">No profiles found.</p>
        @endif
    </div>
</div>

@if($switchRequest->status === 'pending')
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h2 class="font-semibold text-lg mb-4">Add Admin Notes (Optional)</h2>
    <form method="POST" action="{{ route('admin.profile-approval.reject', $switchRequest->id) }}" id="notes-form">
        @csrf
        <textarea name="admin_notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Add notes for the user..."></textarea>
        <p class="text-xs text-gray-500 mt-1">Notes will be visible to the user. Use the Approve/Reject buttons above to finalize.</p>
    </form>
</div>
@endif
@endsection
