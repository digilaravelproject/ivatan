@php
    $type = request()->get('type', 'all');

    $labels = [
        'all'   => 'All Banners',
        'image' => 'Image Banners',
        'video' => 'Video Banners',
    ];

    $pageTitle = $labels[$type] ?? 'Banners';
@endphp

@extends('admin.layouts.app')
@section('title', $pageTitle)

@section('content')
<div class="p-6 mx-auto space-y-8 bg-white rounded-lg shadow max-w-7xl">

    {{-- Navigation Tabs --}}
    <div class="flex justify-around mb-6 border-b">
        @foreach ($labels as $key => $label)
            <a href="{{ route('admin.banners.index', ['type' => $key]) }}"
               class="{{ $type === $key
                    ? 'text-blue-600 border-b-2 border-blue-600'
                    : 'text-gray-600 hover:text-blue-500' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">{{ $pageTitle }}</h2>

        <a href="{{ route('admin.banners.create') }}"
           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            + Add Banner
        </a>
    </div>

    {{-- Banner Grid --}}
    @if ($banners->count())
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

            @foreach ($banners as $banner)
                <div class="overflow-hidden bg-white border rounded-lg shadow-sm">

                    {{-- Media --}}
                    <div class="relative w-full aspect-square bg-gray-100">
                        @if ($banner->type === 'video')
                            <video muted autoplay loop
                                   class="absolute inset-0 object-cover w-full h-full">
                                <source src="{{ asset('storage/'.$banner->file) }}">
                            </video>
                        @else
                            <img src="{{ asset('storage/'.$banner->file) }}"
                                 class="absolute inset-0 object-cover w-full h-full">
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="px-4 py-3 space-y-1">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $banner->title ?? '—' }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ ucfirst($banner->type) }} Banner •
                            {{ $banner->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between px-4 py-2 text-sm border-t">

                        <a href="{{ route('admin.banners.edit', $banner) }}"
                           class="text-blue-600 hover:underline">
                            ✏️ Edit
                        </a>

                        <form action="{{ route('admin.banners.destroy', $banner) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this banner?')">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:underline">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $banners->links('pagination::tailwind') }}
        </div>
    @else
        <div class="py-12 text-center text-gray-500">
            No {{ strtolower($pageTitle) }} available.
        </div>
    @endif

</div>
@endsection
