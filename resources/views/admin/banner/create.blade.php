@extends('admin.layouts.app')
@section('title', 'Add Banner')

@section('content')
<div class="max-w-xl p-6 mx-auto bg-white rounded shadow">

<form method="POST"
      action="{{ route('admin.banners.store') }}"
      enctype="multipart/form-data">

    @csrf

    {{-- Title --}}
    <div class="mb-4">
        <label class="block mb-1 font-medium">Title</label>
        <input type="text" name="title"
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- Type --}}
    <div class="mb-4">
        <label class="block mb-1 font-medium">Banner Type</label>
        <select name="type" class="w-full border rounded px-3 py-2">
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>
    </div>

    {{-- File --}}
    <div class="mb-6">
        <label class="block mb-1 font-medium">Banner File</label>
        <input type="file" name="file"
               class="w-full border rounded px-3 py-2"
               required>
    </div>

    <button class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
        Save Banner
    </button>

</form>
</div>
@endsection
