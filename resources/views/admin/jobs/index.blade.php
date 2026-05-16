@extends('admin.layouts.app')
@section('title', 'Job Posts')

@section('content')
    <div class="p-6 mx-auto space-y-8 bg-white rounded-lg shadow max-w-7xl">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-semibold text-gray-900">Job Posts</h2>

            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title / company"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="published" @selected(request('status') == 'published')>Open</option>
                    <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                    <option value="expired" @selected(request('status') == 'expired')>Expired</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Filter
                </button>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="fixed z-50 w-full max-w-sm p-4 text-white bg-green-600 rounded shadow-lg top-4 right-4">
                <div class="flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-2xl font-bold leading-none">&times;</button>
                </div>
            </div>
        @endif

        {{-- Job Table --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow ring-1 ring-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase select-none">
                            #
                        </th>
                        <th
                            class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase select-none">
                            Title & Details
                        </th>
                        <th
                            class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase select-none">
                            Company
                        </th>
                        <th
                            class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase select-none">
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase select-none">
                            Posted / Updated
                        </th>
                        <th
                            class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase tracking-wider select-none min-w-[170px]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($jobs as $job)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $job->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $job->title }}</div>
                                <div class="mt-1 space-x-2 text-xs text-gray-500">
                                    <span>Slug: <em>{{ $job->slug ?? 'N/A' }}</em></span>
                                    @if ($job->location || $job->country)
                                        <span>| Location: {{ $job->location ?? '-' }}, {{ $job->country ?? '-' }}</span>
                                    @endif
                                    @if ($job->employment_type)
                                        <span>| Type: {{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</span>
                                    @endif
                                    @if ($job->salary_min && $job->salary_max)
                                        <span>| Salary: ₹{{ number_format($job->salary_min) }} -
                                            ₹{{ number_format($job->salary_max) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $job->company_name }}
                                @if (!empty($job->company_email))
                                    <div class="text-xs text-gray-400">{{ $job->company_email }}</div>
                                @elseif(!empty($job->company_website))
                                    <div class="text-xs text-indigo-600 hover:underline">
                                        <a href="{{ $job->company_website }}" target="_blank" rel="noopener noreferrer">
                                            {{ $job->company_website }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusClasses = [
                                        'published' => 'bg-green-100 text-green-700',
                                        'draft' => 'bg-yellow-100 text-yellow-700',
                                        'expired' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded {{ $statusClasses[$job->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $job->status === 'published' ? 'Open' : ucfirst($job->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                <div>Posted: {{ $job->created_at->format('d M, Y') }}</div>
                                <div>Updated: {{ $job->updated_at->format('d M, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                <div class="inline-flex justify-end space-x-2">
                                    <a href="{{ route('admin.jobs.show', $job->id) }}"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 transition rounded bg-blue-50 hover:bg-blue-100">
                                        View
                                    </a>
                                    <a href="{{ route('admin.jobs.edit', $job->id) }}"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-indigo-700 transition rounded bg-indigo-50 hover:bg-indigo-100">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this job?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-700 transition rounded bg-red-50 hover:bg-red-100">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No job posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $jobs->links() }}
        </div>
    </div>
@endsection
