<nav class="mt-5 px-5 space-y-1">
    <a href="{{ route('admin.dashboard.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->routeIs('admin.dashboard.index')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Home Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7v6h10v-6z" />
        </svg>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('admin.users.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->routeIs('admin.users.index')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Users Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span>Users</span>
        {{-- <span class="ml-auto bg-red-500 text-white text-xs px-2 rounded-full">{{ $summary['users_total'] ?? 0 }}</span> --}}
    </a>

    <a href="{{ route('admin.userposts.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->routeIs('admin.post.index')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Posts Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m-6-8h6M21 12c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8z" />
        </svg>
        <span>Posts</span>
    </a>

    <a href="{{ route('admin.products.index') }} "
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors duration-300">
        <!-- Products Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 21V7a2 2 0 00-2-2H6a2 2 0 00-2 2v14" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3v4M8 3v4M3 9h18" />
        </svg>
        <span>Products</span>
    </a>
</nav>
