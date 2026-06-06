@extends('admin.layouts.app')
@section('title', 'Invoices')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">📄 Invoices</h1>
</div>

<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Total</p>
        <p class="text-2xl font-bold">{{ $summary['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-green-500 text-sm">Paid</p>
        <p class="text-2xl font-bold text-green-600">{{ $summary['paid'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-yellow-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-red-500 text-sm">Overdue</p>
        <p class="text-2xl font-bold text-red-600">{{ $summary['overdue'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-blue-500 text-sm">Total Revenue</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total_revenue'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="border rounded px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice # or user..." class="border rounded px-3 py-2 text-sm w-48">
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
            <a href="{{ route('admin.invoices.index') }}" class="text-gray-500 px-3 py-2 text-sm">Clear</a>
        </form>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Due Date</th>
                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($invoices as $invoice)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium">{{ $invoice->invoice_number }}</td>
                <td class="px-4 py-3">
                    <div class="text-sm">{{ $invoice->user?->name }}</div>
                    <div class="text-xs text-gray-500">{{ $invoice->user?->email }}</div>
                </td>
                <td class="px-4 py-3 text-sm font-medium">{{ number_format($invoice->amount, 2) }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm">{{ $invoice->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 text-sm">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No invoices found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4 border-t">{{ $invoices->links() }}</div>
</div>
@endsection
