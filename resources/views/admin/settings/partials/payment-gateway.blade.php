<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold mb-4">💳 Payment Gateway Configuration</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update-payment') }}" id="payment-form">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Active Gateway</label>
            <select name="payment_active_gateway" class="w-full border rounded px-3 py-2">
                <option value="razorpay" @selected($settings['payment_active_gateway'] ?? '' === 'razorpay')>Razorpay</option>
                <option value="stripe" @selected($settings['payment_active_gateway'] ?? '' === 'stripe') disabled class="text-gray-400">Stripe (Coming Soon)</option>
            </select>
        </div>

        <h3 class="font-medium text-sm text-gray-500 uppercase mt-6 mb-3">Razorpay Credentials</h3>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Key ID</label>
                <input type="text" name="services_razorpay_key" value="{{ $settings['services_razorpay_key'] ?? '' }}" class="w-full border rounded px-3 py-2 font-mono text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Key Secret</label>
                <input type="password" name="services_razorpay_secret" value="{{ $settings['services_razorpay_secret'] ?? '' }}" class="w-full border rounded px-3 py-2 font-mono text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Webhook Secret</label>
                <input type="password" name="services_razorpay_webhook_secret" value="{{ $settings['services_razorpay_webhook_secret'] ?? '' }}" class="w-full border rounded px-3 py-2 font-mono text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Webhook URL</label>
                <input type="text" readonly value="{{ route('webhook.razorpay') }}" class="w-full border rounded px-3 py-2 text-sm bg-gray-50 cursor-text">
                <p class="text-xs text-gray-500 mt-1">Configure this URL in your Razorpay dashboard.</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save Payment Settings</button>
            <button type="button" id="test-connection-btn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">🔌 Test Connection</button>
        </div>
    </form>

    <div id="test-result" class="mt-4 hidden"></div>
</div>

@push('scripts')
<script>
document.getElementById('test-connection-btn').addEventListener('click', async function() {
    const btn = this;
    const result = document.getElementById('test-result');
    btn.disabled = true;
    btn.textContent = 'Testing...';
    result.className = 'mt-4 p-3 rounded hidden';

    try {
        const resp = await fetch('{{ route('admin.settings.test-connection') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        });
        const data = await resp.json();
        result.className = 'mt-4 p-3 rounded ' + (data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
        result.textContent = data.success ? '✅ Connection successful!' : '❌ ' + (data.message || 'Connection failed.');
    } catch(e) {
        result.className = 'mt-4 p-3 rounded bg-red-100 text-red-700';
        result.textContent = '❌ Connection error.';
    }
    result.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = '🔌 Test Connection';
});
</script>
@endpush
