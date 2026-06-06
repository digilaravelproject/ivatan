@extends('admin.layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.invoices.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Invoices</a>
        <h1 class="text-2xl font-bold mt-1">Invoice {{ $invoice->invoice_number }}</h1>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">🖨️ Print</button>
        <form method="POST" action="{{ route('admin.invoices.resend', $invoice->id) }}">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">📧 Resend</button>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-8 max-w-3xl mx-auto" id="invoice-print">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="text-2xl font-bold">{{ config('app.name') }}</h2>
            <p class="text-gray-500 text-sm">Invoice</p>
        </div>
        <div class="text-right">
            <h1 class="text-xl font-bold">{{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-gray-500">Date: {{ $invoice->created_at->format('d M Y') }}</p>
            <p class="text-sm text-gray-500">Due: {{ $invoice->due_date?->format('d M Y') ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="font-semibold text-sm text-gray-500 mb-1">Bill To:</h3>
        <p class="font-medium">{{ $invoice->user?->name }}</p>
        <p class="text-sm text-gray-600">{{ $invoice->user?->email }}</p>
        <p class="text-sm text-gray-600">{{ $invoice->user?->phone ?? '' }}</p>
    </div>

    <table class="w-full mb-8">
        <thead>
            <tr class="border-b-2 border-gray-300">
                <th class="text-left py-2 text-sm font-medium text-gray-500">Description</th>
                <th class="text-right py-2 text-sm font-medium text-gray-500">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->items)
                @foreach($invoice->items as $item)
                <tr class="border-b">
                    <td class="py-3">
                        <p class="font-medium">{{ $item['description'] ?? 'Subscription' }}</p>
                        @if(isset($item['period']))
                        <p class="text-xs text-gray-500">Period: {{ $item['period'] }}</p>
                        @endif
                    </td>
                    <td class="py-3 text-right font-medium">{{ number_format($item['amount'] ?? $invoice->amount, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr class="border-b">
                    <td class="py-3 font-medium">{{ $invoice->plan?->name ?? 'Subscription' }}</td>
                    <td class="py-3 text-right font-medium">{{ number_format($invoice->amount, 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td class="py-3 text-right font-bold text-lg">Total:</td>
                <td class="py-3 text-right font-bold text-lg">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="border-t pt-4 text-center text-sm text-gray-500">
        @php $footer = \App\Models\Setting::where('key', 'subscription.invoice_footer')->value('value'); @endphp
        {{ $footer ?? 'Thank you for your business.' }}
    </div>

    <div class="mt-4 text-center">
        @php
        $statusColors = ['paid' => 'bg-green-100 text-green-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'failed' => 'bg-red-100 text-red-800'];
        @endphp
        <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$invoice->status] ?? 'bg-gray-100' }}">
            Status: {{ ucfirst($invoice->status) }}
            @if($invoice->paid_at) · Paid on {{ $invoice->paid_at->format('d M Y H:i') }} @endif
        </span>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #invoice-print, #invoice-print * { visibility: visible; }
    #invoice-print { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>
@endsection
