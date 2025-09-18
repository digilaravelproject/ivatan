<nav class="sticky px-5 mt-5 space-y-1 ">
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
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->is('admin/users*')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Users Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <span>Users</span>
        {{-- <span class="px-2 ml-auto text-xs text-white bg-red-500 rounded-full">{{ $summary['users_total'] ?? 0 }}</span> --}}
    </a>

    <a href="{{ route('admin.userposts.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
        @if (request()->routeIs('admin.userposts.*') || request()->routeIs('admin.post.*')) bg-gray-700 text-white @endif
        transition-colors duration-300">

        <!-- Posts Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z" />
        </svg>

        <span>Posts</span>
    </a>


    <a href="{{ route('admin.products.index') }} "
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->is('admin/products*')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Products Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7" />
        </svg>

        <span>Products</span>
    </a>
    <a href="{{ route('admin.services.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
   @if (request()->is('admin/services*')) bg-gray-700 text-white @endif
   transition-colors duration-300">

        <!-- Products Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 11V9a4 4 0 014-4h2a2 2 0 012 2v2a4 4 0 01-4 4h-2m-2 0v2a4 4 0 004 4h2a2 2 0 002-2v-2a4 4 0 00-4-4h-2" />
        </svg>

        <span>Services</span>
    </a>

    <a href="{{ route('admin.jobs.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
   @if (request()->is('admin/jobs') || request()->is('admin/jobs/*')) bg-gray-700 text-white @endif transition-colors duration-300">

        <!-- Jobs Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m2 0a2 2 0 002-2V7a2 2 0 00-2-2h-3.586A2 2 0 0112 4.586V4a2 2 0 00-2-2H8a2 2 0 00-2 2v1.586A2 2 0 014.586 7H4a2 2 0 00-2 2v3a2 2 0 002 2h2" />
        </svg>

        <span>Jobs</span>
    </a>

    {{-- <div x-data="{ open: {{ request()->routeIs('admin.ads.*') || request()->routeIs('admin.ad.ad-packages.*') ? 'true' : 'false' }} }" class="space-y-1"> --}}
    <div x-data="{ open: false }" x-init="open = {{ request()->routeIs('admin.ads.*') || request()->routeIs('admin.ad.ad-packages.*') ? 'true' : 'false' }}" class="space-y-1">

        <button @click="open = !open"
            class="w-full flex items-center justify-between gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors duration-300
        {{ request()->routeIs('admin.ads.*') || request()->routeIs('admin.ad.ad-packages.*') ? 'bg-gray-700 text-white' : '' }}">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 8V6a4 4 0 00-4-4H6a2 2 0 00-2 2v12a2 2 0 002 2h1l2 3v-3h5a4 4 0 004-4v-2" />
                </svg>

                <span>Ads Management</span>
            </div>
            <!-- Chevron Icon -->
            <svg :class="{ 'rotate-90': open }" class="w-4 h-4 transition-transform transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <div x-show="open" x-collapse class="pl-6 space-y-1">
            <a href="{{ route('admin.ads.index') }}"
                class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ads.index') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                All Ads
            </a>
            <a href="{{ route('admin.ads.pending') }}"
                class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ads.pending') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Pending Approval
            </a>
            <a href="{{ route('admin.ad.ad-packages.index') }}"
                class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ad.ad-packages.*') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Ad Packages
            </a>
        </div>
    </div>

</nav>
