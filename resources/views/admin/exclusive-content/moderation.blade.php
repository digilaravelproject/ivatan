@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Exclusive Content Moderation</h1>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="pending-content-tbody">
                <!-- Data populated via Vue/Axios from API -->
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Loading pending content...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Approval (Content-Specific Fee) -->
<div id="approveModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-6 w-96">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Content</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Override Platform Fee Type</label>
                <select id="fee_type" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="">No Override (Use Default)</option>
                    <option value="percentage">Percentage</option>
                    <option value="flat">Flat Amount</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Override Platform Fee Value</label>
                <input type="number" id="fee_value" class="mt-1 block w-full rounded-md border-gray-300" placeholder="e.g. 5">
            </div>
            <div class="flex justify-end space-x-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded-md">Cancel</button>
                <button onclick="submitApproval()" class="px-4 py-2 bg-blue-600 text-white rounded-md">Approve</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // JS Logic to fetch /api/admin/exclusive/pending-content and populate table
    // JS Logic for approve/reject API calls
</script>
@endpush
