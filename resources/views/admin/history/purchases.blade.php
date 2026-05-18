@extends('admin.layouts.app')
@section('title', "Purchase History - {$user->name}")
@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Purchase History: {{ $user->name }}</h2>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $order->id }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($order->items->isNotEmpty())
                            @foreach($order->items as $item)
                                <div class="text-gray-900">{{ $item->item?->title ?? 'Unknown Product' }}</div>
                                <div class="text-xs text-gray-400">Qty: {{ $item->quantity }} &times; ${{ number_format($item->price, 2) }}</div>
                            @endforeach
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 rounded text-xs font-medium 
                            {{ match($order->status) {
                                'paid' => 'bg-green-100 text-green-700',
                                'delivered' => 'bg-blue-100 text-blue-700',
                                'shipped' => 'bg-yellow-100 text-yellow-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600'
                            } }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
