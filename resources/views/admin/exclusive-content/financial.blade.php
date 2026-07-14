@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Financial Dashboard (Exclusive Content)</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
            <h3 class="text-gray-500 text-sm uppercase font-semibold">Total Platform Revenue</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2" id="stat-revenue">₹0.00</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
            <h3 class="text-gray-500 text-sm uppercase font-semibold">Total Gateway Fees</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2" id="stat-gateway">₹0.00</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500">
            <h3 class="text-gray-500 text-sm uppercase font-semibold">Total Creator Earnings</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2" id="stat-creators">₹0.00</p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Wallet Transactions</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="transactions-tbody">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading transactions...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        loadFinancialStats();
        loadTransactions();
    });

    function loadFinancialStats() {
        $.get('{{ route('admin.exclusive.wallets.stats') }}', function(response) {
            $('#stat-revenue').text(`₹${parseFloat(response.total_platform_revenue || 0).toFixed(2)}`);
            $('#stat-gateway').text(`₹${parseFloat(response.total_gateway_fees || 0).toFixed(2)}`);
            $('#stat-creators').text(`₹${parseFloat(response.total_creator_earnings || 0).toFixed(2)}`);
        });
    }

    function loadTransactions() {
        $('#transactions-tbody').html('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading transactions...</td></tr>');
        
        $.get('{{ route('admin.exclusive.wallets.transactions') }}', function(response) {
            let html = '';
            const data = response.data || [];
            
            if (data.length === 0) {
                html = '<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No recent transactions found.</td></tr>';
            } else {
                data.forEach(txn => {
                    const creatorName = txn.wallet && txn.wallet.user ? txn.wallet.user.name : 'Unknown';
                    const username = txn.wallet && txn.wallet.user ? txn.wallet.user.username : 'Unknown';
                    const amount = txn.amount ? `₹${txn.amount}` : '₹0.00';
                    const type = txn.type || 'credit';
                    const date = txn.created_at ? new Date(txn.created_at).toLocaleString() : '';

                    // Style type badge
                    const typeColor = type === 'credit' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';

                    html += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${creatorName} (@${username})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase ${typeColor}">${type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">${amount}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        </tr>
                    `;
                });
            }
            $('#transactions-tbody').html(html);
        });
    }
</script>
@endpush
