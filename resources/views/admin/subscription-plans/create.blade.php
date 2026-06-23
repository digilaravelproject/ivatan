@extends('admin.layouts.app')
@section('title', 'Create Plan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Plans</a>
        <h1 class="text-2xl font-bold mt-1">Create Subscription Plan</h1>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.subscription-plans.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Profile Type</label>
                <select name="profile_type" required class="w-full border rounded px-3 py-2">
                    @foreach($profileTypes as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" name="slug" required class="w-full border rounded px-3 py-2" value="{{ old('slug') }}" placeholder="e.g., pro-seller">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" step="0.01" min="0" name="price" required class="w-full border rounded px-3 py-2" value="{{ old('price', 0) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency</label>
                <input type="text" name="currency" required class="w-full border rounded px-3 py-2" value="{{ old('currency', 'INR') }}" maxlength="3">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Duration (days)</label>
                <input type="number" min="1" name="duration_days" required class="w-full border rounded px-3 py-2" value="{{ old('duration_days', 30) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sort Order</label>
                <input type="number" min="0" name="sort_order" class="w-full border rounded px-3 py-2" value="{{ old('sort_order', 0) }}">
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    <span class="text-sm">Active</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1" class="rounded">
                    <span class="text-sm">Default Plan</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div class="mb-6">
            <label class="block text-base font-semibold mb-2 border-b pb-1">Plan Features & Limits</label>
            <p class="text-xs text-gray-500 mb-3">Check features to enable them for this plan, and enter their limit/multiplier values.</p>
            
            <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto border rounded p-3 bg-gray-50">
                @foreach($features as $feature)
                <div class="flex items-center justify-between p-2 border-b border-gray-200 bg-white rounded shadow-sm gap-4">
                    <div class="flex items-start gap-2 max-w-[65%]">
                        <input type="checkbox" name="selected_features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}" class="mt-1 rounded">
                        <label for="feat_{{ $feature->id }}" class="text-sm font-medium cursor-pointer">
                            {{ $feature->name }}
                            @if(!$feature->is_implemented)
                            <span class="ml-1 text-[10px] bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded font-normal">Static Placeholder</span>
                            @else
                            <span class="ml-1 text-[10px] bg-green-100 text-green-800 px-1.5 py-0.5 rounded font-normal">Active Feature</span>
                            @endif
                            <span class="block text-xs text-gray-400 font-normal mt-0.5">{{ $feature->description }}</span>
                        </label>
                    </div>
                    <div class="w-[30%]">
                        <input type="text" name="feature_limits[{{ $feature->id }}]" class="w-full border rounded px-2 py-1 text-sm" placeholder="e.g. 1.4x, Medium, 10, Yes" value="">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Plan</button>
    </form>
</div>

<script>
document.querySelector('input[name="name"]').addEventListener('input', function() {
    let slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    document.querySelector('input[name="slug"]').value = slug;
});
</script>
@endsection
