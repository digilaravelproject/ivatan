@extends('admin.layouts.app')
@section('title', 'System Settings')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">⚙️ System Settings</h1>
    <p class="mt-2 text-sm text-gray-500">Manage your system integration keys, payment options, dynamic configs, and subscriptions.</p>
</div>

<div class="mb-6 bg-gray-100 p-1.5 rounded-lg inline-flex gap-1 border border-gray-200">
    <button class="tab-btn px-5 py-2.5 text-sm font-semibold rounded-md transition-all duration-200 shadow-sm bg-white text-blue-600" data-tab="payment">
        💳 Payment Gateway
    </button>
    <button class="tab-btn px-5 py-2.5 text-sm font-semibold rounded-md transition-all duration-200 text-gray-600 hover:text-gray-900" data-tab="subscription">
        🔄 Subscription
    </button>
    <button class="tab-btn px-5 py-2.5 text-sm font-semibold rounded-md transition-all duration-200 text-gray-600 hover:text-gray-900" data-tab="general">
        🏢 General
    </button>
</div>

<div class="transition-opacity duration-300">
    <div id="tab-payment" class="tab-content">@include('admin.settings.partials.payment-gateway')</div>
    <div id="tab-subscription" class="tab-content hidden">@include('admin.settings.partials.subscription')</div>
    <div id="tab-general" class="tab-content hidden">@include('admin.settings.partials.general')</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    function activateTab(tabName) {
        tabButtons.forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.className = "tab-btn px-5 py-2.5 text-sm font-semibold rounded-md transition-all duration-200 shadow-sm bg-white text-blue-600";
            } else {
                btn.className = "tab-btn px-5 py-2.5 text-sm font-semibold rounded-md transition-all duration-200 text-gray-600 hover:text-gray-900";
            }
        });

        tabContents.forEach(content => {
            if (content.id === 'tab-' + tabName) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });
    }

    // Set tab from URL query parameter or hash
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || window.location.hash.replace('#', '') || 'payment';
    activateTab(activeTab);

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            activateTab(tabName);
            // Update URL without full page reload
            const newUrl = window.location.pathname + '?tab=' + tabName;
            window.history.pushState({ path: newUrl }, '', newUrl);
        });
    });
});
</script>
@endpush
