<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold mb-4">🏢 General Settings</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update-general') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Site Name</label>
                <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Admin Email</label>
                <input type="email" name="admin_email" value="{{ $settings['admin_email'] ?? '' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency</label>
                <input type="text" name="app_currency" value="{{ $settings['app_currency'] ?? 'INR' }}" class="w-full border rounded px-3 py-2" maxlength="3">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency Symbol</label>
                <input type="text" name="app_currency_symbol" value="{{ $settings['app_currency_symbol'] ?? '₹' }}" class="w-full border rounded px-3 py-2" maxlength="10">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Profile Approval Required</label>
            <select name="profile_approval_required" class="w-full border rounded px-3 py-2">
                <option value="1" @selected(($settings['profile_approval_required'] ?? '1') === '1')>Yes — Admin must approve profile switches</option>
                <option value="0" @selected(($settings['profile_approval_required'] ?? '') === '0')>No — Auto-approve profile switches</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Default Profile Type for New Users</label>
            <select name="default_profile_type" class="w-full border rounded px-3 py-2">
                <option value="personal" @selected(($settings['default_profile_type'] ?? 'personal') === 'personal')>Personal</option>
                <option value="seller" @selected(($settings['default_profile_type'] ?? '') === 'seller')>Seller</option>
                <option value="creator" @selected(($settings['default_profile_type'] ?? '') === 'creator')>Creator</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save General Settings</button>
    </form>
</div>
