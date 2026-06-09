@extends('admin.layouts.app')
@section('title', 'Edit Plan — ' . $plan->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Plans</a>
        <h1 class="text-2xl font-bold mt-1">Edit: {{ $plan->name }}</h1>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.subscription-plans.update', $plan->id) }}">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Profile Type</label>
                <select name="profile_type" required class="w-full border rounded px-3 py-2">
                    @foreach($profileTypes as $val => $label)
                    <option value="{{ $val }}" @selected($plan->profile_type === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name', $plan->name) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" name="slug" required class="w-full border rounded px-3 py-2" value="{{ old('slug', $plan->slug) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" step="0.01" min="0" name="price" required class="w-full border rounded px-3 py-2" value="{{ old('price', $plan->price) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Currency</label>
                <input type="text" name="currency" required class="w-full border rounded px-3 py-2" value="{{ old('currency', $plan->currency) }}" maxlength="3">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Duration (days)</label>
                <input type="number" min="1" name="duration_days" required class="w-full border rounded px-3 py-2" value="{{ old('duration_days', $plan->duration_days) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sort Order</label>
                <input type="number" min="0" name="sort_order" class="w-full border rounded px-3 py-2" value="{{ old('sort_order', $plan->sort_order) }}">
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked($plan->is_active) class="rounded">
                    <span class="text-sm">Active</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1" @checked($plan->is_default) class="rounded">
                    <span class="text-sm">Default Plan</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $plan->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Features (one per line)</label>
            <textarea name="features" rows="5" class="w-full border rounded px-3 py-2">{{ is_array($plan->features) ? implode("\n", $plan->features) : $plan->features }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Plan</button>
    </form>
</div>
@endsection
