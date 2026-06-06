@extends('admin.layouts.app')
@section('title', 'Subscription Plans')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">💳 Subscription Plans</h1>
    <a href="{{ route('admin.subscription-plans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Plan</a>
</div>

@forelse($plans as $profileType => $typePlans)
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-3 capitalize">{{ $profileType }} Plans</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($typePlans as $plan)
        <div class="bg-white rounded-lg shadow p-6 {{ $plan->is_default ? 'ring-2 ring-blue-500' : '' }}">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-lg">{{ $plan->name }}</h3>
                    <p class="text-2xl font-bold mt-1">
                        {{ number_format($plan->price, 2) }}
                        <span class="text-sm font-normal text-gray-500">/{{ $plan->duration_days >= 36500 ? 'lifetime' : $plan->duration_days . ' days' }}</span>
                    </p>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}" class="text-blue-600 hover:text-blue-800 text-sm p-1">✏️</a>
                    <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" onsubmit="return confirm('Delete this plan?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm p-1">🗑️</button>
                    </form>
                </div>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <p class="capitalize">{{ str_replace('_', ' ', $plan->profile_type) }}</p>
                <p>{{ $plan->description }}</p>
                @if($plan->features)
                <ul class="space-y-1 mt-2">
                    @foreach($plan->features as $feature)
                    <li class="flex items-center gap-2 text-xs">
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            <div class="mt-4 flex gap-2">
                @if($plan->is_default)<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Default</span>@endif
                @if(!$plan->is_active)<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Inactive</span>@endif
                <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ $plan->subscriptions_count ?? $plan->subscriptions()->count() }} subscribers</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
    No subscription plans found. <a href="{{ route('admin.subscription-plans.create') }}" class="text-blue-600 hover:underline">Create one</a>.
</div>
@endforelse
@endsection
