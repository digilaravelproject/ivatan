<div class="bg-white rounded-xl shadow border border-gray-150 p-5 w-full max-w-5xl">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-bold text-gray-800">💳 Payment Gateway Settings</h2>
            <p class="text-xs text-gray-400">Configure active gateways, API keys, and connection details.</p>
        </div>
        <div id="status-badge" class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
            Active Integration
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-lg mb-4 text-xs font-medium">
            <span class="text-emerald-500 font-bold">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-800 px-3 py-2 rounded-lg mb-4 text-xs font-medium">
            <span class="text-rose-500 font-bold">✕</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update-payment') }}" id="payment-form">
        @csrf

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Active Provider Gateway</label>
            <div class="relative">
                <select name="payment_active_gateway" id="payment_active_gateway" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                    <option value="razorpay" @selected(($settings['payment_active_gateway'] ?? '') === 'razorpay')>Razorpay</option>
                    <option value="phonepe" @selected(($settings['payment_active_gateway'] ?? '') === 'phonepe')>PhonePe</option>
                </select>
            </div>
        </div>

        <!-- Razorpay Configuration Panel -->
        <div id="razorpay-section" class="gateway-section bg-gray-50/50 border border-gray-100 p-4 rounded-xl mb-4">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-sm">💳</span>
                <h3 class="font-bold text-gray-700 text-xs tracking-wide uppercase">Razorpay Credentials</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Key ID</label>
                    <input type="text" name="services_razorpay_key" id="services_razorpay_key" value="{{ $settings['services_razorpay_key'] ?? '' }}" placeholder="rzp_test_..." class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">Found under Razorpay API Keys dashboard.</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Key Secret</label>
                    <input type="password" name="services_razorpay_secret" id="services_razorpay_secret" value="{{ $settings['services_razorpay_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">Keep this key secret and stored securely.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Webhook Secret</label>
                    <input type="password" name="services_razorpay_webhook_secret" id="services_razorpay_webhook_secret" value="{{ $settings['services_razorpay_webhook_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">Secret token to authenticate Razorpay events.</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Webhook URL</label>
                    <input type="text" readonly value="{{ route('webhook.razorpay') }}" class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-500 cursor-text select-all font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Copy & configure this endpoint in your Razorpay dashboard.</p>
                </div>
            </div>
        </div>

        <!-- PhonePe Configuration Panel -->
        <div id="phonepe-section" class="gateway-section hidden bg-gray-50/50 border border-gray-100 p-4 rounded-xl mb-4">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-sm">📲</span>
                <h3 class="font-bold text-gray-700 text-xs tracking-wide uppercase">PhonePe Credentials</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Merchant ID / Client ID</label>
                    <input type="text" name="services_phonepe_merchant_id" id="services_phonepe_merchant_id" value="{{ $settings['services_phonepe_merchant_id'] ?? '' }}" placeholder="e.g. M23NCDAG7VSKU_2604301424" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">PhonePe provided Merchant ID (or Client ID for test credentials).</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Salt Key / Client Secret</label>
                    <input type="password" name="services_phonepe_salt_key" id="services_phonepe_salt_key" value="{{ $settings['services_phonepe_salt_key'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">Used to compute SHA256 signatures (or Client Secret for test credentials).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Salt Index / Client Version</label>
                    <input type="text" name="services_phonepe_salt_index" id="services_phonepe_salt_index" value="{{ $settings['services_phonepe_salt_index'] ?? '1' }}" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-gray-400 mt-1">Default is 1 (matches Client Version in dashboard).</p>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Environment Mode</label>
                    <select name="services_phonepe_env" id="services_phonepe_env" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="sandbox" @selected(($settings['services_phonepe_env'] ?? '') === 'sandbox')>Sandbox (Test Mode)</option>
                        <option value="production" @selected(($settings['services_phonepe_env'] ?? '') === 'production')>Production (Live Mode)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Set to Sandbox for mock payments during development.</p>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-[11px] font-bold text-gray-500 mb-1">Webhook URL</label>
                <input type="text" readonly value="{{ route('webhook.phonepe') }}" class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-500 cursor-text select-all font-mono">
                <p class="text-[10px] text-gray-400 mt-1">Configure this endpoint URL in your PhonePe console to capture real-time payment states.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-5 pt-3 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-all text-xs">
                Save Payment Settings
            </button>
            <button type="button" id="test-connection-btn" class="bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all text-xs flex items-center justify-center gap-2">
                <span>🔌</span> Test Connection
            </button>
        </div>
    </form>

    <div id="test-result" class="mt-4 p-3 rounded-lg border hidden transition-all duration-300"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_active_gateway');
    const razorpaySec = document.getElementById('razorpay-section');
    const phonepeSec = document.getElementById('phonepe-section');
    const badge = document.getElementById('status-badge');

    function toggleSections() {
        if (select.value === 'razorpay') {
            razorpaySec.classList.remove('hidden');
            phonepeSec.classList.add('hidden');
            badge.textContent = "Razorpay Mode";
            badge.className = "px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100";
        } else {
            razorpaySec.classList.add('hidden');
            phonepeSec.classList.remove('hidden');
            badge.textContent = "PhonePe Mode";
            badge.className = "px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-100";
        }
    }

    select.addEventListener('change', toggleSections);
    toggleSections();
});

document.getElementById('test-connection-btn').addEventListener('click', async function() {
    const btn = this;
    const result = document.getElementById('test-result');
    const gateway = document.getElementById('payment_active_gateway').value;
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Verification in progress...';
    result.className = 'mt-6 p-4 rounded-xl border hidden transition-all duration-300';

    let bodyData = { gateway: gateway };

    if (gateway === 'razorpay') {
        bodyData.key = document.getElementById('services_razorpay_key').value;
        bodyData.secret = document.getElementById('services_razorpay_secret').value;
        bodyData.webhook_secret = document.getElementById('services_razorpay_webhook_secret').value;
    } else {
        bodyData.key = document.getElementById('services_phonepe_merchant_id').value;
        bodyData.secret = document.getElementById('services_phonepe_salt_key').value;
        bodyData.webhook_secret = document.getElementById('services_phonepe_salt_index').value;
        bodyData.env = document.getElementById('services_phonepe_env').value;
    }

    try {
        const resp = await fetch('{{ route('admin.settings.test-connection') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(bodyData)
        });
        const data = await resp.json();
        
        if (data.success) {
            result.className = 'mt-6 p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-start gap-3 text-sm';
            result.innerHTML = `
                <div class="bg-emerald-100 text-emerald-700 rounded-full p-1 text-xs font-bold leading-none">✓</div>
                <div>
                    <h4 class="font-bold mb-0.5">Connection Successful!</h4>
                    <p class="text-xs opacity-90">${data.message || 'Credentials verified successfully.'}</p>
                </div>
            `;
        } else {
            result.className = 'mt-6 p-4 rounded-xl border bg-rose-50 border-rose-200 text-rose-800 flex items-start gap-3 text-sm';
            result.innerHTML = `
                <div class="bg-rose-100 text-rose-700 rounded-full p-1 text-xs font-bold leading-none">✕</div>
                <div>
                    <h4 class="font-bold mb-0.5">Connection Failed</h4>
                    <p class="text-xs opacity-90">${data.message || 'Verification failed. Please double check credentials.'}</p>
                </div>
            `;
        }
    } catch(e) {
        result.className = 'mt-6 p-4 rounded-xl border bg-rose-50 border-rose-200 text-rose-800 flex items-start gap-3 text-sm';
        result.innerHTML = `
            <div class="bg-rose-100 text-rose-700 rounded-full p-1 text-xs font-bold leading-none">✕</div>
            <div>
                <h4 class="font-bold mb-0.5">Connection Error</h4>
                <p class="text-xs opacity-90">A network or server error prevented the verification request.</p>
            </div>
        `;
    }
    
    result.classList.remove('hidden');
    btn.disabled = false;
    btn.innerHTML = '<span>🔌</span> Test Connection';
});
</script>
@endpush
