<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <div>
            <h2 class="text-xl font-bold text-gray-800">🔄 Subscription & Billing Settings</h2>
            <p class="text-xs text-gray-500 mt-1">Configure default subscription terms, trials, renewals, and invoice footer metadata.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6">
            <span class="text-emerald-500 font-bold">✓</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg mb-6">
            <span class="text-rose-500 font-bold">✕</span>
            <span class="text-sm font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update-subscription') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Default Duration (Days)</label>
                <input type="number" min="1" name="subscription_default_duration" value="{{ $settings['subscription_default_duration'] ?? 30 }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Number of active days for newly purchased plans.</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Trial Period (Days)</label>
                <input type="number" min="0" name="subscription_trial_days" value="{{ $settings['subscription_trial_days'] ?? 0 }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Trial duration before charging begins (set 0 to disable).</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Cancellation Mode</label>
                <select name="subscription_cancellation_mode" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-gray-700">
                    <option value="end_of_period" @selected(($settings['subscription_cancellation_mode'] ?? 'end_of_period') === 'end_of_period')>End of Period (Recommended)</option>
                    <option value="immediate" @selected(($settings['subscription_cancellation_mode'] ?? '') === 'immediate')>Immediate Cancellation</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">End of Period lets users use active plan until it naturally expires.</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Auto Renew Status</label>
                <select name="subscription_auto_renew" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-gray-700">
                    <option value="1" @selected(($settings['subscription_auto_renew'] ?? '1') === '1')>Enabled (Renew Automatically)</option>
                    <option value="0" @selected(($settings['subscription_auto_renew'] ?? '') === '0')>Disabled (Manually Renew)</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Controls the system-wide default renewal behavior.</p>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-1">Grace Period (Days)</label>
            <input type="number" min="0" name="subscription_grace_period" value="{{ $settings['subscription_grace_period'] ?? 7 }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-400 mt-1">Allow access to active features for a certain number of days post-expiry before termination.</p>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-1">Invoice Footer Content</label>
            <textarea name="subscription_invoice_footer" rows="3" placeholder="Thank you for subscribing..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $settings['subscription_invoice_footer'] ?? 'Thank you for your business.' }}</textarea>
            <p class="text-xs text-gray-400 mt-1">This message will appear at the bottom of all generated transaction PDFs.</p>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm">
                Save Subscription Settings
            </button>
        </div>
    </form>
</div>
