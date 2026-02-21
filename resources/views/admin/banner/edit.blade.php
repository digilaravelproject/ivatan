@extends('admin.layouts.app')
@section('title', 'Edit Banner')

@section('content')
<div class="max-w-xl p-6 mx-auto bg-white rounded shadow">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('admin.banners.index') }}"
           class="inline-flex items-center px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
            ← Back
        </a>
    </div>

    <form method="POST"
          action="{{ route('admin.banners.update', $banner) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Title</label>
            <input type="text"
                   name="title"
                   value="{{ old('title', $banner->title) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- Type --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Banner Type</label>
            <select name="type" class="w-full border rounded px-3 py-2">
                <option value="image" {{ $banner->type === 'image' ? 'selected' : '' }}>
                    Image
                </option>
                <option value="video" {{ $banner->type === 'video' ? 'selected' : '' }}>
                    Video
                </option>
            </select>
        </div>

        {{-- Current Banner Preview --}}
        <div class="mb-4">
            <label class="block mb-2 font-medium">Current Banner</label>

            <div class="relative w-full overflow-hidden bg-gray-100 rounded aspect-video">

                @if ($banner->type === 'video')
                    <video controls
                           class="absolute inset-0 object-contain w-full h-full">
                        <source src="{{ asset('storage/'.$banner->file) }}">
                    </video>
                @else
                    <img src="{{ asset('storage/'.$banner->file) }}"
                         class="absolute inset-0 object-contain w-full h-full">
                @endif

            </div>
        </div>

        {{-- Replace File --}}
        <div class="mb-6">
            <label class="block mb-1 font-medium">
                Replace Banner <span class="text-xs text-gray-500">(optional)</span>
            </label>
            <input type="file"
                   name="file"
                   class="w-full border rounded px-3 py-2">

            <p class="mt-1 text-xs text-gray-500">
                Leave empty to keep existing banner.
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">
                Update Banner
            </button>

            <a href="{{ route('admin.banners.index') }}"
               class="px-5 py-2 text-sm border rounded">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
