@extends('admin.layouts.app')
@section('title', 'Subscriptions — ' . $user->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Subscriptions</a>
        <h1 class="text-2xl font-bold mt-1">
            <span class="flex items-center gap-3">
                <img src="{{ $user->profile_photo_url }}" alt="" class="w-8 h-8 rounded-full">
                {{ $user->name }}'s Subscriptions
            </span>
        </h1>
        <p class="text-gray-500 text-sm">{{ $user->email }} · {{ $user->username }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        @forelse($user->subscriptions as $sub)
        <div class="bg-white rounded-lg shadow p-4 {{ $sub->isActive() ? 'border-l-4 border-green-500' : '' }}">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <span class="font-semibold">{{ $sub->plan?->name }}</span>
                    <span class="text-sm text-gray-500 ml-2">({{ ucfirst($sub->profile?->type) }})</span>
                </div>
                @php
                $statusColors = ['active' => 'bg-green-100 text-green-800', 'past_due' => 'bg-yellow-100 text-yellow-800', 'cancelled' => 'bg-red-100 text-red-800', 'expired' => 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$sub->status] ?? 'bg-gray-100' }}">{{ ucfirst($sub->status) }}</span>
            </div>
            <div class="grid grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <span class="text-xs text-gray-400">Started</span>
                    <p>{{ $sub->starts_at?->format('d M Y') }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400">Ends</span>
                    <p>{{ $sub->ends_at?->format('d M Y') ?? 'Lifetime' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400">Amount</span>
                    <p class="font-medium">{{ number_format($sub->plan?->price ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Details →</a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            This user has no subscriptions yet.
        </div>
        @endforelse
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Manual Assign Plan</h2>
            <form method="POST" action="{{ route('admin.subscriptions.assign') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Profile</label>
                    <select name="profile_id" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select profile...</option>
                        @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}">{{ ucfirst($profile->type) }} {{ $profile->is_active ? '(active)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Plan</label>
                    <select name="subscription_plan_id" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select plan...</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->price, 2) }} ({{ $plan->profile_type }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Assign Plan</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">User Info</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Phone</dt>
                    <dd>{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Joined</dt>
                    <dd>{{ $user->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Verified</dt>
                    <dd>{{ $user->is_verified ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
