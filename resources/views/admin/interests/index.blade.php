@extends('admin.layouts.app')

@section('title', 'Manage Interests')

@section('content')
    <div class="max-w-6xl px-4 py-8 mx-auto space-y-10 sm:px-6 lg:px-8">

        {{-- Section Title --}}
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
            <span class="text-indigo-600">Manage</span> Interests & Categories
        </h1>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- 1. Create Category Form (Larger and more distinct) --}}
            <div class="h-full p-6 bg-white shadow-lg lg:col-span-1 rounded-xl">
                <h2 class="pb-3 mb-5 text-xl font-semibold text-gray-800 border-b">
                    <i class="mr-2 text-green-500 fas fa-folder-plus"></i> Create New Category
                </h2>
                <form action="{{ route('admin.interests.category.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="name" placeholder="Category Name (e.g., IT, Sports)"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center items-center px-4 py-2.5 text-lg font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Add Category
                    </button>
                </form>
            </div>

            {{-- 2. Add New Interest Form (Spanning two columns) --}}
            <div class="p-6 bg-white shadow-lg lg:col-span-2 rounded-xl">
                <h2 class="pb-3 mb-5 text-xl font-semibold text-gray-800 border-b">
                    <i class="mr-2 text-indigo-500 fas fa-tag"></i> Add New Interest Tag
                </h2>
                <form action="{{ route('admin.interests.store') }}" method="POST" class="flex flex-col gap-4 md:flex-row">
                    @csrf

                    {{-- Category Dropdown --}}
                    <select name="interest_category_id"
                        class="w-full md:w-1/3 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        required>
                        <option value="">— Select Category —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('interest_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Interest Name --}}
                    <input type="text" name="name" placeholder="Interest Name (e.g., Web Development)"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        required value="{{ old('name') }}">

                    <button type="submit"
                        class="w-full md:w-auto px-6 py-2.5 text-lg font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Add Interest
                    </button>
                </form>
                {{-- Error Messages --}}
                <div class="mt-2 space-y-1">
                    @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('interest_category_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- 3. List Table --}}
        <div class="overflow-hidden bg-white shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="mr-2 text-gray-500 fas fa-list-ul"></i> Current Interests List
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-1/4 px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase">
                                Category
                            </th>
                            <th class="w-2/4 px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase">
                                Interest Name
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase w-1/8">
                                Users
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold tracking-wider text-right text-gray-600 uppercase w-1/8">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($interests as $interest)
                            <tr class="transition duration-150 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-indigo-600 whitespace-nowrap">
                                    {{ $interest->category->name ?? 'Uncategorized' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $interest->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ number_format($interest->users_count) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                    <form action="{{ route('admin.interests.destroy', $interest) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to permanently delete the interest: {{ $interest->name }}? This cannot be undone.');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 transition duration-150 ease-in-out hover:text-red-900 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-lg text-center text-gray-500 bg-gray-50">
                                    <i class="mr-2 fas fa-exclamation-circle"></i> No interests found. Start by adding one above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t bg-gray-50">
                {{ $interests->links() }}
            </div>
        </div>

    </div>
@endsection
