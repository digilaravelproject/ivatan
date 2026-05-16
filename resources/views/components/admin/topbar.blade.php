<div class="flex items-center justify-between bg-gray-800 text-white px-4 py-3 shadow-md">
    <!-- Page title -->
    <div class="text-lg font-semibold">
        @isset($title)
            {{ $title }}
        @else
            Admin Panel
        @endisset
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <button class="p-2 rounded-lg hover:bg-gray-700 transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>

        <!-- Profile dropdown -->
        <div class="relative">
            <button id="profileBtn" class="flex items-center gap-2 rounded-lg hover:bg-gray-700 px-2 py-1 transition-colors duration-300">
                <!-- Profile Photo -->
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile"  loading="lazy" class="w-8 h-8 rounded-full object-cover border-2 border-white">
                {{ auth()->user()->name }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Dropdown -->
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded shadow-lg py-2">
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-200">
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile"  loading="lazy" class="w-6 h-6 rounded-full object-cover">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const btn = document.getElementById('profileBtn');
    const dropdown = document.getElementById('profileDropdown');
    btn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });
</script>
