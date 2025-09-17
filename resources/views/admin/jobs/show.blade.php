@extends('admin.layouts.app')
@section('title', $job->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Job Title --}}
    <h1 class="text-3xl font-bold text-gray-900">{{ $job->title }}</h1>

    {{-- Job Info --}}
    <div class="grid grid-cols-1 gap-4 text-gray-700 sm:grid-cols-2">
        <div><strong>Company:</strong> {{ $job->company_name }}</div>

        <div>
            <strong>Status:</strong>
            @if ($job->status === 'published')
                <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                    Open
                </span>
            @else
                <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                    {{ ucfirst($job->status) }}
                </span>
            @endif
        </div>

        <div><strong>Location:</strong> {{ $job->location }}, {{ $job->country }}</div>

        <div>
            <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
        </div>

        <div>
            <strong>Salary:</strong>
            @if($job->salary_min && $job->salary_max)
                ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
            @else
                Not specified
            @endif
        </div>
    </div>

    {{-- Description --}}
    <div>
        <h2 class="mb-2 text-xl font-semibold text-gray-800">Description</h2>
        <p class="text-gray-700 whitespace-pre-line">{!! e($job->description) !!}</p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3 pt-6 border-t">
        <a href="{{ route('admin.jobs.applications', $job->id) }}"
           class="px-4 py-2 text-white transition bg-indigo-600 rounded hover:bg-indigo-700">
            View Applications ({{ $job->applications()->count() }})
        </a>

        <a href="{{ route('admin.jobs.edit', $job->id) }}"
           class="px-4 py-2 text-white transition bg-yellow-500 rounded hover:bg-yellow-600">
            Edit Job
        </a>

        <form method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}" onsubmit="return confirm('Are you sure to delete this job?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2 text-white transition bg-red-600 rounded hover:bg-red-700">
                Delete Job
            </button>
        </form>

        <a href="{{ route('admin.jobs.index') }}"
           class="px-4 py-2 text-gray-700 transition bg-gray-200 rounded hover:bg-gray-300">
            Back to Job List
        </a>
    </div>

</div>
@endsection
