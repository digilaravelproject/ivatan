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
</div>
@endsection

@push('scripts')
<script>
    // Fetch global settings and Creator Enablement Requests from APIs
</script>
@endpush
