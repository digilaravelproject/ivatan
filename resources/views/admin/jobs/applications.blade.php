@extends('admin.layouts.app')
@section('title', 'Job Applications for: ' . $job->title)

@section('content')
    <div class="p-6 mx-auto space-y-6 max-w-7xl">

        <h2 class="text-3xl font-semibold text-gray-900">Applications for: {{ $job->title }}</h2>

        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-md">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-semibold tracking-wide text-left text-gray-700">Applicant</th>
                        <th class="px-6 py-3 font-semibold tracking-wide text-left text-gray-700">Message</th>
                        <th class="px-6 py-3 font-semibold tracking-wide text-left text-gray-700">Status</th>
                        <th class="px-6 py-3 font-semibold tracking-wide text-left text-gray-700">Resume</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $application)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $application->applicant->name }}
                            </td>
                            <td class="max-w-xl px-6 py-4 text-gray-700 truncate" title="{{ $application->cover_message }}">
                                {{ Str::limit($application->cover_message, 80) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $application->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($application->resume_path)
                                    <a href="{{ route('admin.applications.resume', $application->id) }}"
                                        class="font-medium text-indigo-600 hover:underline">
                                        Download
                                    </a>
                                @else
                                    <span class="italic text-gray-400">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 italic text-center text-gray-500">
                                No applications found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $applications->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
