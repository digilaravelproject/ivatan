@extends('admin.layouts.app')
@section('title', 'Edit Job Post')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Edit Job: {{ $job->title }}</h2>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-gray-600 hover:underline">← Back to Jobs</a>
        </div>

        <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST"
            class="p-6 space-y-5 bg-white rounded-lg shadow">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Job Title <span
                        class="text-red-500">*</span></label>
                <input type="text" name="title" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('title', $job->title) }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Company Name (disabled) --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Company Name</label>
                <input type="text" disabled class="w-full bg-gray-100 border-gray-200 rounded-lg shadow-sm"
                    value="{{ old('company_name', $job->company_name) }}">
                <p class="mt-1 text-sm text-gray-500">Company name is usually set on creation and not editable.</p>
            </div>

            {{-- Location --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('location', $job->location) }}">
            </div>

            {{-- Country --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Country</label>
                <input type="text" name="country"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('country', $job->country) }}">
            </div>

            {{-- Employment Type --}}
            <div class="mb-4">
                <label for="employment_type" class="block mb-1 text-sm font-semibold text-gray-700">
                    Employment Type
                </label>
                <select id="employment_type" name="employment_type"
                    class="block w-full px-3 py-2 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    @php
                        $types = [
                            'full_time' => 'Full Time',
                            'part_time' => 'Part Time',
                            'contract' => 'Contract',
                            'temporary' => 'Temporary',
                            'internship' => 'Internship',
                        ];
                    @endphp
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('employment_type', $job->employment_type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('employment_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Salary Min --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Salary Min</label>
                <input type="number" name="salary_min" min="0" step="any"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('salary_min', $job->salary_min) }}">
            </div>

            {{-- Salary Max --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Salary Max</label>
                <input type="number" name="salary_max" min="0" step="any"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200"
                    value="{{ old('salary_max', $job->salary_max) }}">
            </div>

            {{-- Status --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
                @php
                    $statuses = ['published' => 'Published', 'draft' => 'Draft', 'expired' => 'Expired'];
                @endphp
                <select name="status" required
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $job->status) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Job Description</label>
                <textarea name="description" rows="6"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-200">{{ old('description', $job->description) }}</textarea>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.jobs.index') }}"
                    class="px-4 py-2 text-gray-600 transition border rounded-lg hover:bg-gray-100">Cancel</a>
                <button type="submit"
                    class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">Update Job</button>
            </div>
        </form>
    </div>
@endsection
