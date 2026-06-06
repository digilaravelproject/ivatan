<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold mb-4">🔄 Subscription Settings</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update-subscription') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Default Subscription Duration (days)</label>
                <input type="number" min="1" name="subscription_default_duration" value="{{ $settings['subscription_default_duration'] ?? 30 }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Trial Period (days)</label>
                <input type="number" min="0" name="subscription_trial_days" value="{{ $settings['subscription_trial_days'] ?? 0 }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Cancellation Mode</label>
                <select name="subscription_cancellation_mode" class="w-full border rounded px-3 py-2">
                    <option value="end_of_period" @selected(($settings['subscription_cancellation_mode'] ?? 'end_of_period') === 'end_of_period')>End of Period (Recommended)</option>
                    <option value="immediate" @selected(($settings['subscription_cancellation_mode'] ?? '') === 'immediate')>Immediate</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Auto Renew</label>
                <select name="subscription_auto_renew" class="w-full border rounded px-3 py-2">
                    <option value="1" @selected(($settings['subscription_auto_renew'] ?? '1') === '1')>Enabled</option>
                    <option value="0" @selected(($settings['subscription_auto_renew'] ?? '') === '0')>Disabled</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Invoice Footer Text</label>
            <textarea name="subscription_invoice_footer" rows="2" class="w-full border rounded px-3 py-2">{{ $settings['subscription_invoice_footer'] ?? 'Thank you for your business.' }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Grace Period (days after expiry)</label>
            <input type="number" min="0" name="subscription_grace_period" value="{{ $settings['subscription_grace_period'] ?? 7 }}" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save Subscription Settings</button>
    </form>
</div>
