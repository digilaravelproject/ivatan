@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Exclusive Content Moderation</h1>

    {{-- Tab Toggles --}}
    <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg shadow-sm">
        <button onclick="switchTab('pending')" id="tab-pending" class="px-6 py-3 border-b-2 border-indigo-600 text-indigo-600 font-bold text-sm focus:outline-none transition-all duration-200">
            Pending Content
        </button>
        <button onclick="switchTab('approved')" id="tab-approved" class="px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-300 font-semibold text-sm focus:outline-none transition-all duration-200">
            Approved Content
        </button>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-b-lg">
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
<div id="approveModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
        <div class="bg-white rounded-lg shadow-xl p-6 w-96 relative z-10">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Content</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Override Platform Fee Type</label>
                <select id="fee_type" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="">No Override (Use Default)</option>
                    <option value="percentage">Percentage</option>
                    <option value="flat">Flat Amount</option>
                </select>
            </div>
            <div class="mb-4" id="fee_value_container" style="display: none;">
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

<!-- Modal for Post Preview -->
<div id="previewModal" class="hidden fixed z-40 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closePreviewModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Post Details & Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Flex container for 2 columns - fixed height on desktop to prevent screen scrolling -->
            <div class="flex flex-col md:flex-row h-[70vh] min-h-[400px] max-h-[600px] overflow-hidden">
                <!-- Left Column: Media Preview (Fits inside the container) -->
                <div id="preview-media-container" class="w-full md:w-1/2 bg-black flex justify-center items-center h-full overflow-hidden border-r border-gray-100">
                    <!-- Populated via Javascript -->
                </div>

                <!-- Right Column: Content Details and Actions -->
                <div class="w-full md:w-1/2 p-6 flex flex-col justify-between h-full bg-white">
                    <div class="space-y-4 overflow-y-auto pr-1">
                        <!-- Creator Profile & Details -->
                        <div class="flex items-center space-x-3">
                            <img id="preview-avatar" class="w-12 h-12 rounded-full object-cover border-2 border-indigo-100" src="" alt="Creator Avatar">
                            <div>
                                <h4 class="font-semibold text-gray-800" id="preview-creator-name"></h4>
                                <p class="text-xs text-indigo-600" id="preview-creator-handle"></p>
                            </div>
                        </div>

                        <!-- Price and Type Badge -->
                        <div class="flex space-x-2">
                            <span id="preview-type-badge" class="px-3 py-1 text-xs font-semibold rounded-full uppercase"></span>
                            <span id="preview-price-badge" class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full"></span>
                        </div>

                        <!-- Platform Fee Breakdown Section -->
                        <div id="preview-fee-breakdown" class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 hidden">
                            <h5 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">Override Platform Fee Details</h5>
                            <div class="space-y-1 text-sm text-indigo-950">
                                <div class="flex justify-between">
                                    <span>Creator Base Price:</span>
                                    <span class="font-semibold" id="breakdown-base-price">₹0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Override Platform Fee:</span>
                                    <span class="font-semibold" id="breakdown-override-fee">No Override</span>
                                </div>
                                <div class="flex justify-between border-t border-indigo-200 pt-1 mt-1 font-bold text-indigo-900">
                                    <span>Final Buyer Price:</span>
                                    <span id="breakdown-final-price">₹0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Caption -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700 text-sm whitespace-pre-line" id="preview-caption"></p>
                        </div>
                    </div>

                    <!-- Actions Footer -->
                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <button onclick="closePreviewModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md font-medium text-sm transition">Close</button>
                        <div class="flex space-x-2" id="preview-action-buttons">
                            <!-- Dynamic Approve/Reject buttons -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let activePostId = null;
    let pendingPosts = [];
    let activeTab = 'pending';

    $(document).ready(function() {
        loadPendingContent();

        // Listen for fee type dropdown changes
        $('#fee_type').on('change', function() {
            const val = $(this).val();
            if (val === 'percentage' || val === 'flat') {
                $('#fee_value_container').show();
            } else {
                $('#fee_value_container').hide();
                $('#fee_value').val('');
            }
        });
    });

    function switchTab(tab) {
        activeTab = tab;
        if (tab === 'pending') {
            $('#tab-pending').addClass('border-indigo-600 text-indigo-600 font-bold').removeClass('border-transparent text-gray-500 font-semibold');
            $('#tab-approved').addClass('border-transparent text-gray-500 font-semibold').removeClass('border-indigo-600 text-indigo-600 font-bold');
            loadPendingContent();
        } else {
            $('#tab-approved').addClass('border-indigo-600 text-indigo-600 font-bold').removeClass('border-transparent text-gray-500 font-semibold');
            $('#tab-pending').addClass('border-transparent text-gray-500 font-semibold').removeClass('border-indigo-600 text-indigo-600 font-bold');
            loadApprovedContent();
        }
    }

    function loadPendingContent() {
        $('#pending-content-tbody').html('<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Loading pending content...</td></tr>');
        
        $.get('{{ route('admin.exclusive.pending.list') }}', function(response) {
            let html = '';
            pendingPosts = response.data || [];
            
            if (pendingPosts.length === 0) {
                html = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No pending exclusive content found.</td></tr>';
            } else {
                pendingPosts.forEach(post => {
                    const username = post.user ? post.user.username : 'Unknown';
                    const creatorName = post.user ? post.user.name : 'Unknown';
                    const price = post.price ? `₹${post.price}` : '₹0.00';
                    const type = post.type || 'post';

                    html += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${creatorName} (@${username})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase">${type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">${price}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button onclick="showPreview(${post.id})" class="text-blue-600 hover:text-blue-900 font-semibold flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View Post
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openApproveModal(${post.id})" class="text-green-600 hover:text-green-900 font-semibold mr-3">Approve</button>
                                <button onclick="rejectContent(${post.id})" class="text-red-600 hover:text-red-900 font-semibold">Reject</button>
                            </td>
                        </tr>
                    `;
                });
            }
            
            $('thead tr').html(`
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Price</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            `);
            $('#pending-content-tbody').html(html);
        });
    }

    function loadApprovedContent() {
        $('#pending-content-tbody').html('<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Loading approved content...</td></tr>');
        
        $.get('{{ route('admin.exclusive.approved.list') }}', function(response) {
            let html = '';
            pendingPosts = response.data || [];
            
            if (pendingPosts.length === 0) {
                html = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No approved exclusive content found.</td></tr>';
            } else {
                pendingPosts.forEach(post => {
                    const username = post.user ? post.user.username : 'Unknown';
                    const creatorName = post.user ? post.user.name : 'Unknown';
                    const price = post.price ? `₹${post.price}` : '₹0.00';
                    const type = post.type || 'post';

                    let approvedAt = 'N/A';
                    if (post.updated_at) {
                        const dateObj = new Date(post.updated_at);
                        approvedAt = dateObj.toLocaleString('en-IN', { 
                            dateStyle: 'medium', 
                            timeStyle: 'short' 
                        });
                    }

                    html += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${creatorName} (@${username})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase">${type}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">${price}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${approvedAt}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button onclick="showPreview(${post.id})" class="text-blue-600 hover:text-blue-900 font-semibold flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View Post
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="rejectContent(${post.id})" class="text-red-600 hover:text-red-900 font-semibold">Reject</button>
                            </td>
                        </tr>
                    `;
                });
            }
            
            $('thead tr').html(`
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            `);
            $('#pending-content-tbody').html(html);
        });
    }

    function showPreview(postId) {
        const post = pendingPosts.find(p => p.id === postId);
        if (!post) return;

        const username = post.user ? post.user.username : 'Unknown';
        const creatorName = post.user ? post.user.name : 'Unknown';
        const avatar = post.user && post.user.profile_photo_url ? post.user.profile_photo_url : '/images/default-avatar.png';
        const price = post.price ? `₹${post.price}` : '₹0.00';
        const type = post.type || 'post';

        $('#preview-creator-name').text(creatorName);
        $('#preview-creator-handle').text('@' + username);
        $('#preview-avatar').attr('src', avatar);
        
        $('#preview-type-badge').text(type)
            .removeClass()
            .addClass('px-3 py-1 text-xs font-semibold rounded-full uppercase ' + 
                (type === 'reel' ? 'bg-purple-100 text-purple-800' : 
                 type === 'video' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'));
        
        $('#preview-price-badge').text(price);
        $('#preview-caption').text(post.caption || 'No caption provided.');

        // Reset and hide breakdown by default
        $('#preview-fee-breakdown').addClass('hidden');

        // Show breakdown if post is already approved
        if (post.exclusive_status === 'approved' && post.platform_fee_breakdown) {
            const breakdown = post.platform_fee_breakdown;
            const basePrice = parseFloat(breakdown.creator_price || 0);
            const platformFee = parseFloat(breakdown.platform_fee || 0);
            const finalPrice = parseFloat(breakdown.final_price || 0);

            let feeString = '';
            if (post.override_platform_fee !== null && post.override_platform_fee !== undefined && post.override_platform_fee_type) {
                const feeVal = parseFloat(post.override_platform_fee);
                const feeType = post.override_platform_fee_type;
                feeString = feeType === 'percentage' ? `${feeVal}% (₹${platformFee.toFixed(2)})` : `₹${platformFee.toFixed(2)} (Flat)`;
            } else {
                feeString = `₹${platformFee.toFixed(2)} (Uses Global/User Settings)`;
            }

            $('#breakdown-base-price').text(`₹${basePrice.toFixed(2)}`);
            $('#breakdown-override-fee').text(feeString);
            $('#breakdown-final-price').text(`₹${finalPrice.toFixed(2)}`);
            $('#preview-fee-breakdown').removeClass('hidden');
        }

        // Render Media
        let mediaHtml = '';
        if (post.images && post.images.length > 0) {
            post.images.forEach(img => {
                mediaHtml += `<img src="${img.original_url}" class="object-contain w-full h-full max-h-full" alt="Post Image">`;
            });
        } else if (post.videos && post.videos.length > 0) {
            post.videos.forEach(vid => {
                mediaHtml += `
                    <video controls controlsList="nodownload" oncontextmenu="return false;" class="w-full h-full max-h-full object-contain">
                        <source src="${vid.original_url}" type="${vid.mime_type || 'video/mp4'}">
                        Your browser does not support the video tag.
                    </video>
                `;
            });
        } else {
            mediaHtml = '<p class="text-gray-400 py-10">No Media Files Available</p>';
        }

        $('#preview-media-container').html(mediaHtml);

        // Render Actions inside preview depending on active tab
        if (activeTab === 'pending') {
            $('#preview-action-buttons').html(`
                <button onclick="closePreviewModal(); openApproveModal(${post.id})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium text-sm transition">Approve</button>
                <button onclick="closePreviewModal(); rejectContent(${post.id})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium text-sm transition">Reject</button>
            `);
        } else {
            $('#preview-action-buttons').html(`
                <button onclick="closePreviewModal(); rejectContent(${post.id})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium text-sm transition">Reject</button>
            `);
        }

        $('#previewModal').removeClass('hidden');
    }

    function closePreviewModal() {
        $('#preview-media-container video').each(function() {
            this.pause();
        });
        $('#previewModal').addClass('hidden');
    }

    function openApproveModal(id) {
        activePostId = id;
        $('#fee_type').val('');
        $('#fee_value').val('');
        $('#fee_value_container').hide();
        $('#approveModal').removeClass('hidden');
    }

    function closeModal() {
        $('#approveModal').addClass('hidden');
        activePostId = null;
    }

    function submitApproval() {
        if (!activePostId) return;

        const feeType = $('#fee_type').val();
        const feeValue = $('#fee_value').val();

        $.ajax({
            url: `/admin/exclusive/pending-content/${activePostId}/approve`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                override_platform_fee_type: feeType || null,
                override_platform_fee: feeValue || null
            },
            success: function(response) {
                Swal.fire({
                    title: 'Approved!',
                    text: 'Content approved successfully!',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
                closeModal();
                if (activeTab === 'pending') loadPendingContent();
                else loadApprovedContent();
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Error: ' + (xhr.responseJSON?.message || 'Failed to approve'),
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    function rejectContent(id) {
        Swal.fire({
            title: 'Reject Content',
            text: 'Enter reason for rejection (Required):',
            input: 'text',
            inputPlaceholder: 'Reason...',
            showCancelButton: true,
            confirmButtonText: 'Submit Rejection',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            inputValidator: (value) => {
                if (!value) {
                    return 'Rejection reason is required!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value;
                $.ajax({
                    url: `/admin/exclusive/pending-content/${id}/reject`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        rejection_reason: reason
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Rejected!',
                            text: 'Content status updated to Rejected successfully!',
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        });
                        if (activeTab === 'pending') loadPendingContent();
                        else loadApprovedContent();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Error: ' + (xhr.responseJSON?.message || 'Failed to reject'),
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
