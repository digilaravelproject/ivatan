@extends('admin.layouts.app')
@section('title', '{{ $package->name }} Package Details')

@section('content')
    <div class="max-w-4xl py-6 mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Package Details</h2>
            <a href="{{ route('admin.ad.ad-packages.index') }}"
                class="px-4 py-2 text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Back to Packages
            </a>
        </div>

        {{-- Package Card --}}
        <div class="p-6 space-y-4 bg-white rounded-lg shadow">

            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">{{ $package->name }}</h3>
                <span class="text-sm text-gray-500">ID: {{ $package->id }}</span>
            </div>

            <p class="text-gray-700">{{ $package->description }}</p>

            <div class="grid grid-cols-2 gap-6 text-gray-700">
                <div>
                    <h4 class="font-medium text-gray-900">Price</h4>
                    <p>₹ {{ number_format($package->price, 2) }} {{ $package->currency }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Reach Limit</h4>
                    <p>{{ $package->reach_limit }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Duration</h4>
                    <p>{{ $package->duration_days }} days</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Target Location</h4>
                    <p>{{ $package->targeting['location'] ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="space-y-1 text-sm text-gray-500">
                <div><strong>Created At:</strong> {{ \Carbon\Carbon::parse($package->created_at)->format('d M, Y H:i') }}
                </div>
                <div><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($package->updated_at)->format('d M, Y H:i') }}
                </div>
            </div>

        </div>

    </div>
@endsection
