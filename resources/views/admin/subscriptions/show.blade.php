@extends('admin.layouts.app')
@section('title', 'Subscription #' . $subscription->id)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Subscriptions</a>
        <h1 class="text-2xl font-bold mt-1">Subscription #{{ $subscription->id }}</h1>
    </div>
    <div class="flex gap-2">
        @if($subscription->isActive())
        <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Cancel Subscription</button>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Subscription Details</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">Status</dt>
                    <dd>
                        @php
                        $statusColors = ['active' => 'bg-green-100 text-green-800', 'past_due' => 'bg-yellow-100 text-yellow-800', 'cancelled' => 'bg-red-100 text-red-800', 'expired' => 'bg-gray-100 text-gray-800', 'pending' => 'bg-blue-100 text-blue-800'];
                        $color = $statusColors[$subscription->status] ?? 'bg-gray-100';
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $color }}">{{ ucfirst($subscription->status) }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Plan</dt>
                    <dd class="font-medium">{{ $subscription->plan?->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Price</dt>
                    <dd class="font-medium">{{ number_format($subscription->plan?->price ?? 0, 2) }} {{ $subscription->plan?->currency ?? 'INR' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Duration</dt>
                    <dd class="font-medium">{{ $subscription->plan?->duration_days ?? 0 }} days</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Started</dt>
                    <dd>{{ $subscription->starts_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Ends</dt>
                    <dd>{{ $subscription->ends_at?->format('d M Y H:i') ?? 'Never (Lifetime)' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Next Billing</dt>
                    <dd>{{ $subscription->next_billing_at?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Auto Renew</dt>
                    <dd>{{ $subscription->auto_renew ? 'Yes' : 'No' }}</dd>
                </div>
                @if($subscription->cancelled_at)
                <div>
                    <dt class="text-xs text-gray-500">Cancelled At</dt>
                    <dd>{{ $subscription->cancelled_at?->format('d M Y H:i') }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-xs text-gray-500">Cancellation Reason</dt>
                    <dd>{{ $subscription->cancellation_reason ?? 'No reason provided' }}</dd>
                </div>
                @endif
                @if($subscription->gateway_subscription_id)
                <div class="col-span-2">
                    <dt class="text-xs text-gray-500">Gateway Subscription ID</dt>
                    <dd class="text-sm font-mono">{{ $subscription->gateway_subscription_id }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Invoices</h2>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">#</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Amount</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Status</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Date</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($subscription->invoices as $invoice)
                    <tr>
                        <td class="px-3 py-2 text-sm">{{ $invoice->invoice_number }}</td>
                        <td class="px-3 py-2 text-sm">{{ number_format($invoice->amount, 2) }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">No invoices generated yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">User</h2>
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ $subscription->user?->profile_photo_url }}" alt="" class="w-10 h-10 rounded-full">
                <div>
                    <p class="font-medium">{{ $subscription->user?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->user?->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.subscriptions.user', $subscription->user_id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View All Subscriptions →</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Profile</h2>
            <p class="text-sm capitalize">{{ $subscription->profile?->type ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500">Status: {{ $subscription->profile?->status }}</p>
        </div>

        @if($subscription->plan?->features)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Plan Features</h2>
            <ul class="space-y-2">
                @foreach($subscription->plan->features as $feature)
                <li class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<div id="cancelModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Cancel Subscription</h3>
        <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Cancellation Mode</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="mode" value="end_of_period" checked class="text-blue-600">
                        <span class="text-sm">Cancel at period end (no refund — access until {{ $subscription->ends_at?->format('d M Y') ?? 'end of period' }})</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="mode" value="immediate" class="text-red-600">
                        <span class="text-sm">Cancel immediately (stops access now)</span>
                    </label>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Reason (optional)</label>
                <textarea name="reason" rows="3" class="w-full border rounded px-3 py-2 text-sm" placeholder="Why is this being cancelled?"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Close</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>
@endsection
