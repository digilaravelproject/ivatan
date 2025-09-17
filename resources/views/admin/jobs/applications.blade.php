@extends('admin.layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-7xl">
    <h2 class="mb-6 text-2xl font-semibold">Applications for: {{ $job->title }}</h2>

    <div class="overflow-x-auto bg-white border border-gray-200 rounded shadow">
        <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600">Applicant</th>
                    <th class="px-4 py-3 text-left text-gray-600">Message</th>
                    <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-gray-600">Resume</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($applications as $application)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $application->applicant->name }}</td>
                        <td class="max-w-xl px-4 py-2 text-gray-700 truncate" title="{{ $application->cover_message }}">
                            {{ Str::limit($application->cover_message, 60) }}
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded
                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $application->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if($application->resume_path)
                                <a href="{{ route('admin.applications.resume', $application->id) }}"
                                   class="text-indigo-600 hover:underline">Download</a>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">No applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>
@endsection
