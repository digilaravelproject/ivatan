@extends('admin.layouts.app')
@section('title', 'Settings')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">⚙️ Settings</h1>
</div>

<div class="mb-4">
    <div class="flex border-b">
        <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600" data-tab="payment">Payment Gateway</button>
        <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="subscription">Subscription</button>
        <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="general">General</button>
    </div>
</div>

<div id="tab-payment" class="tab-content">@include('admin.settings.partials.payment-gateway')</div>
<div id="tab-subscription" class="tab-content hidden">@include('admin.settings.partials.subscription')</div>
<div id="tab-general" class="tab-content hidden">@include('admin.settings.partials.general')</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-blue-600', 'text-blue-600');
            b.classList.add('text-gray-500');
        });
        this.classList.add('border-blue-600', 'text-blue-600');
        this.classList.remove('text-gray-500');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
    });
});
</script>
@endpush
