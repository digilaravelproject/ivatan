@extends('admin.layouts.app')
@section('title', 'Job Details')

@section('content')
    <div class="max-w-4xl p-8 mx-auto space-y-8 bg-white rounded-lg shadow">

        {{-- Job Title --}}
        <h1 class="text-3xl font-bold text-gray-900">{{ $job->title }}</h1>

        {{-- Job Info --}}
        <div class="grid grid-cols-1 gap-6 text-gray-700 sm:grid-cols-2">
            <div>
                <span class="font-semibold">Company:</span> {{ $job->company_name }}
            </div>

            <div>
                <span class="font-semibold">Status:</span>
                @if ($job->status === 'published')
                    <span class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                        Open
                    </span>
                @else
                    <span class="inline-block px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                        {{ ucfirst($job->status) }}
                    </span>
                @endif
            </div>

            <div>
                <span class="font-semibold">Location:</span> {{ $job->location }}, {{ $job->country }}
            </div>

            <div>
                <span class="font-semibold">Employment Type:</span>
                {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
            </div>

            <div class="sm:col-span-2">
                <span class="font-semibold">Salary:</span>
                @if ($job->salary_min && $job->salary_max)
                    ₹{{ number_format($job->salary_min) }} - ₹{{ number_format($job->salary_max) }}
                @else
                    Not specified
                @endif
            </div>
        </div>

        {{-- Description --}}
        <div>
            <h2 class="pb-2 mb-3 text-xl font-semibold text-gray-800 border-b">Job Description</h2>
            <p class="leading-relaxed text-gray-700 whitespace-pre-line">{!! e($job->description) !!}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">

            <a href="{{ route('admin.jobs.applications', $job->id) }}"
                class="px-5 py-2 font-medium text-white transition bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                View Applications ({{ $job->applications()->count() }})
            </a>

            <a href="{{ route('admin.jobs.edit', $job->id) }}"
                class="px-5 py-2 font-medium text-white transition rounded-lg shadow-sm bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                Edit Job
            </a>

            <form method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}"
                onsubmit="return confirm('Are you sure you want to delete this job?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-5 py-2 font-medium text-white transition bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                    Delete Job
                </button>
            </form>

            <a href="{{ route('admin.jobs.index') }}"
                class="px-5 py-2 font-medium text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-1">
                Back to Job List
            </a>
        </div>

    </div>
@endsection
