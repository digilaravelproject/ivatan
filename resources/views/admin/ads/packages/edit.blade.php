@extends('admin.layouts.app')
@section('title', 'Edit Ad Package')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Edit Ad Package</h2>
            <a href="{{ route('admin.ad.ad-packages.index') }}" class="text-sm text-gray-600 hover:underline">← Back to
                Packages</a>
        </div>

        <form id="packageForm" action="{{ route('admin.ad.ad-packages.update', $adPackage) }}" method="POST"
            class="p-6 space-y-5 bg-white rounded-lg shadow" novalidate>
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="name" class="block mb-1 text-sm font-medium text-gray-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input id="name" type="text" name="name" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('name') border-red-500 @enderror"
                    value="{{ old('name', $adPackage->name) }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block mb-1 text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('description') border-red-500 @enderror">{{ old('description', $adPackage->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Price --}}
            <div>
                <label for="price" class="block mb-1 text-sm font-medium text-gray-700">
                    Price (₹) <span class="text-red-500">*</span>
                </label>
                <input id="price" type="number" name="price" required step="0.01" min="0"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('price') border-red-500 @enderror"
                    value="{{ old('price', $adPackage->price) }}">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration --}}
            <div>
                <label for="duration_days" class="block mb-1 text-sm font-medium text-gray-700">
                    Duration (days) <span class="text-red-500">*</span>
                </label>
                <input id="duration_days" type="number" name="duration_days" required min="1" step="1"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('duration_days') border-red-500 @enderror"
                    value="{{ old('duration_days', $adPackage->duration_days) }}">
                @error('duration_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reach Limit --}}
            <div>
                <label for="reach_limit" class="block mb-1 text-sm font-medium text-gray-700">
                    Reach Limit <span class="text-red-500">*</span>
                </label>
                <input id="reach_limit" type="number" name="reach_limit" required min="1" step="1"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('reach_limit') border-red-500 @enderror"
                    value="{{ old('reach_limit', $adPackage->reach_limit) }}">
                @error('reach_limit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Targeting (JSON) --}}
            <div>
                <label for="targeting" class="block mb-1 text-sm font-medium text-gray-700">Targeting (JSON)</label>
                <textarea id="targeting" name="targeting" rows="4" placeholder='{"location": "India"}'
                    class="w-full font-mono text-xs border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200 @error('targeting') border-red-500 @enderror">{{ old('targeting', json_encode($adPackage->targeting)) }}</textarea>
                <p id="jsonError" class="hidden mt-1 text-sm text-red-600">Invalid JSON format.</p>
                @error('targeting')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.ad.ad-packages.index') }}"
                    class="px-4 py-2 text-gray-600 transition border rounded-lg hover:bg-gray-100">Cancel</a>
                <button id="submitBtn" type="submit"
                    class="px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">Update
                    Package</button>
            </div>
        </form>
    </div>

    {{-- Live JSON validation script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const targetingInput = document.getElementById('targeting');
            const jsonError = document.getElementById('jsonError');
            const submitBtn = document.getElementById('submitBtn');

            function validateJSON() {
                const val = targetingInput.value.trim();
                if (!val) {
                    jsonError.classList.add('hidden');
                    submitBtn.disabled = false;
                    return;
                }
                try {
                    JSON.parse(val);
                    jsonError.classList.add('hidden');
                    submitBtn.disabled = false;
                } catch {
                    jsonError.classList.remove('hidden');
                    submitBtn.disabled = true;
                }
            }

            targetingInput.addEventListener('input', validateJSON);
            validateJSON();
        });
    </script>

    {{-- Positive number validation for price, duration, reach limit --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const priceInput = document.querySelector('input[name="price"]');
            const durationInput = document.querySelector('input[name="duration_days"]');
            const reachLimitInput = document.querySelector('input[name="reach_limit"]');
            const submitBtn = document.getElementById('submitBtn');

            function validatePositiveNumber(input, min) {
                const val = input.value.trim();
                if (val === '') return true; // required will catch empty
                return Number(val) >= min;
            }

            function validateForm() {
                let valid = true;
                if (!validatePositiveNumber(priceInput, 0)) valid = false;
                if (!validatePositiveNumber(durationInput, 1)) valid = false;
                if (!validatePositiveNumber(reachLimitInput, 1)) valid = false;

                // Also check if JSON is valid (using your previous function)
                const jsonValid = (() => {
                    try {
                        const val = document.getElementById('targeting').value.trim();
                        if (!val) return true;
                        JSON.parse(val);
                        return true;
                    } catch {
                        return false;
                    }
                })();

                submitBtn.disabled = !(valid && jsonValid);
            }

            [priceInput, durationInput, reachLimitInput].forEach(input => {
                input.addEventListener('input', () => {
                    validateForm();
                    input.reportValidity();
                });
            });

            validateForm();
        });
    </script>
@endsection
