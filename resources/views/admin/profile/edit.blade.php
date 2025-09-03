@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-xl mt-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Profile</h2>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Profile Photo -->
            <div class="flex items-center gap-6">
                <div class="flex-shrink-0">
                    <img id="photoPreview" src="{{ auth()->user()->profile_photo_url }}" alt="Profile Photo"
                        class="w-24 h-24 rounded-full object-cover border-4 border-indigo-600 shadow-md">

                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <input type="file" name="profile_photo" id="photoInput" class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 shadow-sm">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 shadow-sm">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 shadow-sm">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 shadow-sm">
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Profile Photo Preview Script --}}
    <script>
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');

        photoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    photoPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
