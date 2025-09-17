@extends('admin.layouts.app')
@section('title', 'Edit Job: ' . $job->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold text-gray-900">Edit Job: {{ $job->title }}</h1>

    @if ($errors->any())
        <div class="p-4 mb-6 text-red-700 bg-red-100 rounded">
            <ul class="space-y-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jobs.update', $job->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Job Title --}}
        <div>
            <label for="title" class="block mb-1 font-medium text-gray-700">Job Title <span class="text-red-600">*</span></label>
            <input type="text" name="title" id="title" required
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300"
                value="{{ old('title', $job->title) }}" />
        </div>

        {{-- Company Name (disabled) --}}
        <div>
            <label for="company_name" class="block mb-1 font-medium text-gray-700">Company Name</label>
            <input type="text" name="company_name" id="company_name" disabled
                class="w-full px-3 py-2 bg-gray-100 border rounded"
                value="{{ old('company_name', $job->company_name) }}" />
            <p class="mt-1 text-sm text-gray-500">Company name is usually set on creation and not editable.</p>
        </div>

        {{-- Location --}}
        <div>
            <label for="location" class="block mb-1 font-medium text-gray-700">Location</label>
            <input type="text" name="location" id="location"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300"
                value="{{ old('location', $job->location) }}" />
        </div>

        {{-- Country --}}
        <div>
            <label for="country" class="block mb-1 font-medium text-gray-700">Country</label>
            <input type="text" name="country" id="country"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300"
                value="{{ old('country', $job->country) }}" />
        </div>

        {{-- Employment Type --}}
        <div>
            <label for="employment_type" class="block mb-1 font-medium text-gray-700">Employment Type</label>
            <select name="employment_type" id="employment_type"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300">
                @php
                    $types = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'internship' => 'Internship'];
                @endphp
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('employment_type', $job->employment_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Salary Min --}}
        <div>
            <label for="salary_min" class="block mb-1 font-medium text-gray-700">Salary Min</label>
            <input type="number" name="salary_min" id="salary_min" min="0" step="any"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300"
                value="{{ old('salary_min', $job->salary_min) }}" />
        </div>

        {{-- Salary Max --}}
        <div>
            <label for="salary_max" class="block mb-1 font-medium text-gray-700">Salary Max</label>
            <input type="number" name="salary_max" id="salary_max" min="0" step="any"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300"
                value="{{ old('salary_max', $job->salary_max) }}" />
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="block mb-1 font-medium text-gray-700">Status</label>
            @php
                $statuses = ['published' => 'Published', 'draft' => 'Draft', 'expired' => 'Expired'];
            @endphp
            <select name="status" id="status" required
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300">
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $job->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block mb-1 font-medium text-gray-700">Job Description</label>
            <textarea name="description" id="description" rows="6"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-indigo-300">{{ old('description', $job->description) }}</textarea>
        </div>

        {{-- Buttons --}}
        <div class="flex flex-wrap gap-4">
            <button type="submit"
                class="px-6 py-2 font-semibold text-white transition bg-green-600 rounded hover:bg-green-700">
                Update Job
            </button>

            <a href="{{ route('admin.jobs.index') }}"
                class="px-6 py-2 font-semibold text-gray-700 transition bg-gray-200 rounded hover:bg-gray-300">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
