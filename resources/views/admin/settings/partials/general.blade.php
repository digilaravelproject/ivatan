<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
        <div>
            <h2 class="text-xl font-bold text-gray-800">🏢 General Settings</h2>
            <p class="text-xs text-gray-500 mt-1">Configure general system identities, defaults, approvals, and transaction currencies.</p>
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

    <form method="POST" action="{{ route('admin.settings.update-general') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Site Name</label>
                <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Global name for the platform.</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Admin Notification Email</label>
                <input type="email" name="admin_email" value="{{ $settings['admin_email'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Primary address for system notifications & alerts.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">System Currency ISO</label>
                <input type="text" name="app_currency" value="{{ $settings['app_currency'] ?? 'INR' }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono" maxlength="3">
                <p class="text-xs text-gray-400 mt-1">Standard 3-letter currency identifier (e.g. INR, USD).</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Currency Symbol</label>
                <input type="text" name="app_currency_symbol" value="{{ $settings['app_currency_symbol'] ?? '₹' }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="10">
                <p class="text-xs text-gray-400 mt-1">Symbol used on visual invoices & price labels (e.g. ₹, $).</p>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-1">Profile Approval Sequence</label>
            <select name="profile_approval_required" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-gray-700">
                <option value="1" @selected(($settings['profile_approval_required'] ?? '1') === '1')>Yes — Admin must approve profile switches</option>
                <option value="0" @selected(($settings['profile_approval_required'] ?? '') === '0')>No — Auto-approve profile switches</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">If enabled, switching user modes requires admin moderation.</p>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-bold text-gray-700 mb-1">Default Profile Type for New Users</label>
            <select name="default_profile_type" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-gray-700">
                <option value="personal" @selected(($settings['default_profile_type'] ?? 'personal') === 'personal')>Personal Profile</option>
                <option value="seller" @selected(($settings['default_profile_type'] ?? '') === 'seller')>Seller Profile</option>
                <option value="creator" @selected(($settings['default_profile_type'] ?? '') === 'creator')>Creator Profile</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">Default state initialized for newly registered users.</p>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm">
                Save General Settings
            </button>
        </div>
    </form>
</div>
