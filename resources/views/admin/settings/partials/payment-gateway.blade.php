<div class="bg-white rounded-xl shadow border border-gray-150 p-5 w-full max-w-5xl">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
        <div>
            <h2 class="text-base font-bold text-gray-800">Payment Gateway Settings</h2>
            <p class="text-[11px] text-gray-400">Configure active gateways, API keys, and connection details.</p>
        </div>
        <div id="status-badge" class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
            Active Gateway
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-lg mb-4 text-xs font-medium">
            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-800 px-3 py-2 rounded-lg mb-4 text-xs font-medium">
            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
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
        <div id="razorpay-section" class="gateway-section bg-gray-50/50 border border-gray-100 p-4 rounded-xl mb-4">
            <div class="flex items-center gap-2 mb-3">
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
                    <div class="relative">
                        <input type="password" name="services_razorpay_secret" id="services_razorpay_secret" value="{{ $settings['services_razorpay_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                        <button type="button" onclick="togglePassword('services_razorpay_secret', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="eye-open h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed h-3.5 w-3.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Keep this key secret and stored securely.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Webhook Secret</label>
                    <div class="relative">
                        <input type="password" name="services_razorpay_webhook_secret" id="services_razorpay_webhook_secret" value="{{ $settings['services_razorpay_webhook_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                        <button type="button" onclick="togglePassword('services_razorpay_webhook_secret', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="eye-open h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed h-3.5 w-3.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
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
                    <div class="relative">
                        <input type="password" name="services_phonepe_salt_key" id="services_phonepe_salt_key" value="{{ $settings['services_phonepe_salt_key'] ?? '' }}" placeholder="••••••••••••••••" class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2 font-mono text-xs focus:ring-1 focus:ring-blue-500">
                        <button type="button" onclick="togglePassword('services_phonepe_salt_key', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="eye-open h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed h-3.5 w-3.5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
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
                    <select name="services_phonepe_env" id="services_phonepe_env" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 font-semibold text-gray-700">
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
            <button type="button" id="test-connection-btn" class="bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all text-xs flex items-center justify-center gap-2">
                Test Connection
            </button>
        </div>
    </form>

    <div id="test-result" class="mt-4 p-3 rounded-lg border hidden transition-all duration-300"></div>
</div>

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const openEye = btn.querySelector('.eye-open');
    const closedEye = btn.querySelector('.eye-closed');
    if (input.type === 'password') {
        input.type = 'text';
        openEye.classList.add('hidden');
        closedEye.classList.remove('hidden');
    } else {
        input.type = 'password';
        openEye.classList.remove('hidden');
        closedEye.classList.add('hidden');
    }
}

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
            badge.className = "px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100";
        } else {
            razorpaySec.classList.add('hidden');
            phonepeSec.classList.remove('hidden');
            badge.textContent = "PhonePe Mode";
            badge.className = "px-2.5 py-0.5 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-100";
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
    btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Verifying...';
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
            result.className = 'mt-4 p-3 rounded-lg border bg-emerald-50 border-emerald-200 text-emerald-800 flex items-start gap-2.5 text-xs font-medium';
            result.innerHTML = `
                <svg class="h-4 w-4 text-emerald-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <h4 class="font-bold mb-0.5">Connection Successful</h4>
                    <p class="text-[11px] opacity-90">${data.message || 'Gateway connected successfully.'}</p>
                </div>
            `;
        } else {
            result.className = 'mt-4 p-3 rounded-lg border bg-rose-50 border-rose-200 text-rose-800 flex items-start gap-2.5 text-xs font-medium';
            result.innerHTML = `
                <svg class="h-4 w-4 text-rose-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <div>
                    <h4 class="font-bold mb-0.5">Verification Failed</h4>
                    <p class="text-[11px] opacity-90">${data.message || 'Please verify API credentials.'}</p>
                </div>
            `;
        }
    } catch(e) {
        result.className = 'mt-4 p-3 rounded-lg border bg-rose-50 border-rose-200 text-rose-800 flex items-start gap-2.5 text-xs font-medium';
        result.innerHTML = `
            <svg class="h-4 w-4 text-rose-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <div>
                <h4 class="font-bold mb-0.5">Connection Error</h4>
                <p class="text-[11px] opacity-90">A network or server error occurred.</p>
            </div>
        `;
    }
    
    result.classList.remove('hidden');
    btn.disabled = false;
    btn.innerHTML = 'Test Connection';
});
</script>
@endpush
