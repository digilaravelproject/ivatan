@extends('admin.layouts.app')
@section('title', 'Create Ad Package')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Create Ad Package</h2>
            <a href="{{ route('admin.ad.ad-packages.index') }}" class="text-sm text-gray-600 hover:underline">← Back to
                Packages</a>
        </div>

        <form id="packageForm" action="{{ route('admin.ad.ad-packages.store') }}" method="POST"
            class="p-6 space-y-5 bg-white rounded-lg shadow">
            @csrf

            {{-- Title --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    rows="4">{{ old('description') }}</textarea>
            </div>

            {{-- Price --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Price (₹) <span
                        class="text-red-500">*</span></label>
                <input type="number" name="price" required step="0.01"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('price') }}">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Duration (days) <span
                        class="text-red-500">*</span></label>
                <input type="number" name="duration_days" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('duration_days') }}">
            </div>

            {{-- Reach Limit --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Reach Limit <span
                        class="text-red-500">*</span></label>
                <input type="number" name="reach_limit" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('reach_limit') }}">
            </div>

            {{-- Targeting --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Targeting (JSON)</label>
                <textarea id="targeting" name="targeting"
                    class="w-full font-mono text-xs border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    rows="4" placeholder='{"location": "India"}'>{{ old('targeting') }}</textarea>
                <p id="jsonError" class="hidden mt-1 text-sm text-red-600">Invalid JSON format.</p>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.ad.ad-packages.index') }}"
                    class="px-4 py-2 text-gray-600 transition border rounded-lg hover:bg-gray-100">Cancel</a>
                <button id="submitBtn" type="submit"
                    class="px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">Save Package</button>
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
                    // Empty is allowed, hide error, enable submit
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

            // Initial check on page load (in case old value is invalid)
            validateJSON();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const priceInput = document.querySelector('input[name="price"]');
            const durationInput = document.querySelector('input[name="duration_days"]');
            const reachLimitInput = document.querySelector('input[name="reach_limit"]');
            const submitBtn = document.getElementById('submitBtn');

            function validatePositiveNumber(input) {
                const val = input.value.trim();
                if (val === '') return true; // empty handled by required in backend
                return Number(val) >= 0;
            }

            function validateForm() {
                let valid = true;
                if (!validatePositiveNumber(priceInput)) {
                    priceInput.setCustomValidity('Price cannot be negative');
                    valid = false;
                } else {
                    priceInput.setCustomValidity('');
                }

                if (!validatePositiveNumber(durationInput) || Number(durationInput.value) < 1) {
                    durationInput.setCustomValidity('Duration must be at least 1');
                    valid = false;
                } else {
                    durationInput.setCustomValidity('');
                }

                if (!validatePositiveNumber(reachLimitInput) || Number(reachLimitInput.value) < 1) {
                    reachLimitInput.setCustomValidity('Reach limit must be at least 1');
                    valid = false;
                } else {
                    reachLimitInput.setCustomValidity('');
                }

                submitBtn.disabled = !valid;
            }

            // Listen on input events
            [priceInput, durationInput, reachLimitInput].forEach(input => {
                input.addEventListener('input', () => {
                    validateForm();
                    // Show native validation messages on blur
                    input.reportValidity();
                });
            });

            validateForm(); // Initial validation on page load
        });
    </script>

@endsection
