@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Exclusive Content Settings</h1>

    <!-- Global Settings -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Global Settings</h3>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Global Enablement Fee (₹)</label>
                <input type="number" id="global_enablement_fee" class="mt-1 block w-1/3 rounded-md border-gray-300" value="999">
                <p class="text-sm text-gray-500 mt-1">Fee charged to creators when they request to enable Exclusive Content.</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Global Platform Fee Type</label>
                <select id="global_fee_type" class="mt-1 block w-1/3 rounded-md border-gray-300">
                    <option value="percentage">Percentage (%)</option>
                    <option value="flat">Flat Amount (₹)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Global Platform Fee Value</label>
                <input type="number" id="global_fee_value" class="mt-1 block w-1/3 rounded-md border-gray-300" value="2">
                <p class="text-sm text-gray-500 mt-1">Default fee taken by the platform per transaction.</p>
            </div>

            <button onclick="saveGlobalSettings()" class="px-4 py-2 bg-blue-600 text-white rounded-md mt-4">Save Global Settings</button>
        </div>
    </div>

    <!-- Creator Enablement Requests -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Creator Enablement Requests</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Paid</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="enablements-tbody">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading requests...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal for Enablement Approval (Platform-wide or Creator-Specific Fee) -->
    <div id="approveEnablementModal" class="hidden fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-75">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl p-6 w-96 border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4 font-semibold">Approve Creator Enablement</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Override Platform Fee Type</label>
                    <select id="enablement_override_fee_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">No Override (Use Default)</option>
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount (₹)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Override Platform Fee Value</label>
                    <input type="number" id="enablement_override_fee_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g. 5">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Admin Notes</label>
                    <textarea id="enablement_admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Optional notes..."></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button onclick="closeApproveModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium">Cancel</button>
                    <button onclick="submitEnablementApproval()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">Approve</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        loadSettings();
        loadEnablementRequests();
    });

    function loadSettings() {
        $.get('{{ route('admin.exclusive.settings.values') }}', function(response) {
            $('#global_enablement_fee').val(response.exclusive_content_enablement_fee);
            $('#global_fee_type').val(response.exclusive_content_global_fee_type);
            $('#global_fee_value').val(response.exclusive_content_global_fee_value);
        });
    }

    function saveGlobalSettings() {
        const fee = $('#global_enablement_fee').val();
        const type = $('#global_fee_type').val();
        const val = $('#global_fee_value').val();

        $.ajax({
            url: '{{ route('admin.exclusive.settings.update') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                exclusive_content_enablement_fee: fee,
                exclusive_content_global_fee_type: type,
                exclusive_content_global_fee_value: val
            },
            success: function(response) {
                alert('Global settings saved successfully!');
                loadSettings();
            },
            error: function(xhr) {
                alert('Failed to save settings: ' + (xhr.responseJSON?.message || 'Error occurred'));
            }
        });
    }

    function loadEnablementRequests() {
        $('#enablements-tbody').html('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading requests...</td></tr>');
        
        $.get('{{ route('admin.exclusive.enablements.list') }}', function(response) {
            let html = '';
            const data = response.data || [];
            
            if (data.length === 0) {
                html = '<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No pending enablement requests found.</td></tr>';
            } else {
                data.forEach(req => {
                    const username = req.user ? req.user.username : 'Unknown';
                    const feePaid = req.fee_paid ? `₹${req.fee_paid}` : '₹0.00';
                    const paymentStatus = req.payment_status || 'none';
                    
                    // Show status styling
                    let statusBadge = '';
                    if (req.status === 'approved') {
                        statusBadge = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>';
                    } else if (req.status === 'rejected') {
                        statusBadge = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
                    } else if (req.status === 'pending') {
                        statusBadge = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Approval</span>';
                    } else {
                        statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">${req.status}</span>`;
                    }

                    let actionsHtml = '';
                    if (req.status === 'pending') {
                        actionsHtml = `
                            <button onclick="approveEnablement(${req.id})" class="text-indigo-600 hover:text-indigo-900 font-semibold mr-3">Approve</button>
                            <button onclick="rejectEnablement(${req.id})" class="text-red-600 hover:text-red-900 font-semibold">Reject</button>
                        `;
                    } else {
                        actionsHtml = '<span class="text-gray-400">No actions available</span>';
                    }

                    html += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${req.user ? req.user.name : 'Unknown'} (@${username})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${feePaid} (Payment: ${paymentStatus})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${statusBadge}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${actionsHtml}</td>
                        </tr>
                    `;
                });
            }
            $('#enablements-tbody').html(html);
        });
    }

    let activeEnablementId = null;

    function approveEnablement(id) {
        activeEnablementId = id;
        $('#enablement_override_fee_type').val('');
        $('#enablement_override_fee_value').val('');
        $('#enablement_admin_notes').val('');
        $('#approveEnablementModal').removeClass('hidden');
    }

    function closeApproveModal() {
        $('#approveEnablementModal').addClass('hidden');
        activeEnablementId = null;
    }

    function submitEnablementApproval() {
        if (!activeEnablementId) return;

        const feeType = $('#enablement_override_fee_type').val();
        const feeValue = $('#enablement_override_fee_value').val();
        const notes = $('#enablement_admin_notes').val();

        $.ajax({
            url: `/admin/exclusive/enablements/${activeEnablementId}/approve`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                override_platform_fee_type: feeType || null,
                override_platform_fee: feeValue || null,
                admin_notes: notes || null
            },
            success: function(response) {
                alert('Enablement request approved successfully!');
                closeApproveModal();
                loadEnablementRequests();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to approve'));
            }
        });
    }

    function rejectEnablement(id) {
        const reason = prompt("Enter rejection reason (Required):");
        if (!reason) {
            alert('Rejection reason is required!');
            return;
        }

        $.ajax({
            url: `/admin/exclusive/enablements/${id}/reject`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                admin_notes: reason
            },
            success: function(response) {
                alert('Enablement request rejected successfully!');
                loadEnablementRequests();
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to reject'));
            }
        });
    }
</script>
@endpush
