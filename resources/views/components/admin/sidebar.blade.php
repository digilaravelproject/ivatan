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
        {{-- <span class="px-2 ml-auto text-xs text-white bg-red-500 rounded-full">{{ $summary['users_total'] ?? 0
            }}</span> --}}
    </a>
    <a href="{{ route('admin.interests.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->is('admin/interests*')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Users Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <span>Interests Management</span>

    </a>

    <?php /*<a href="{{ route('admin.userpost.index') }}" class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
        @if (request()->routeIs('admin.userpost.*') || request()->routeIs('admin.posts.*')) bg-gray-700 text-white @endif
        transition-colors duration-300">

        <!-- Posts Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z" />
        </svg>

        <span>Posts</span>
    </a> */?>

    <div
            x-data="{ open: false }"
            x-init="open = {{ request()->routeIs('admin.userpost.*') || request()->routeIs('admin.posts.*') ? 'true' : 'false' }}"
            class="space-y-1"
        >
            <!-- Parent Button -->
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between gap-2 py-2 px-4 rounded-lg
                hover:bg-gray-700 transition-colors duration-300
                {{ request()->routeIs('admin.userpost.*') || request()->routeIs('admin.posts.*') ? 'bg-gray-700 text-white' : '' }}"
            >
                <div class="flex items-center gap-2">
                    <!-- Posts Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16h8M8 12h8m-8-4h8M4 6h16v12H4z" />
                    </svg>

                    <span>Manage Posts</span>
                </div>

                <!-- Chevron -->
                <svg
                    :class="{ 'rotate-90': open }"
                    class="w-4 h-4 transition-transform transform"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Sub Menu -->
            <div x-show="open" x-collapse class="pl-6 space-y-1">

                <!-- Total Posts -->
                <a
                    href="{{ route('admin.userpost.index') }}"
                    class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
                    {{ request()->routeIs('admin.userpost.index') ? 'bg-gray-700 text-white' : 'text-gray-500' }}"
                >
                    Total Posts
                </a>

                <!-- Reported Posts -->
                <a
                    href="{{ route('admin.reported-post.index') }}"
                    class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
                    {{ request()->routeIs('admin.reported-post.index') ? 'bg-gray-700 text-white' : 'text-gray-500' }}"
                >
                    Reported Posts
                </a>

            </div>
        </div>

    <a href="{{ route('admin.products.index') }} "
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 @if (request()->is('admin/products*')) bg-gray-700 text-white @endif transition-colors duration-300">
        <!-- Products Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7" />
        </svg>

        <span>Products</span>
    </a>
    <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
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

    <a href="{{ route('admin.notifications.index') }}"
    class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
    @if (request()->is('admin/notifications*')) bg-gray-700 text-white @endif
    transition-colors duration-300">

        <!-- Bell Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <span>Notifications</span>
    </a>

    <a href="{{ route('admin.live-chat-groups.index') }}"
    class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
    @if (request()->is('admin/live-chat-groups*')) bg-gray-700 text-white @endif
    transition-colors duration-300">

        <!-- Live Chat Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>

        <span>Live Chat Groups</span>
    </a>

    <a href="{{ route('admin.server-health.index') }}"
    class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
    @if (request()->is('admin/server-health*')) bg-gray-700 text-white @endif
    transition-colors duration-300">

        <!-- Heartbeat Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>

        <span>Server Health</span>
    </a>

    <a href="{{ route('admin.banners.index') }}"
    class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
    @if (request()->routeIs('admin.banners.*')) bg-gray-700 text-white @endif
    transition-colors duration-300">

        <!-- Banners Icon -->
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4h16v6H4zM4 14h16v6H4z" />
        </svg>

        <span>Banners</span>
    </a>

    <!-- Subscriptions Accordion -->
    <div x-data="{ open: false }"
        x-init="open = {{ request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.subscription-plans.*') || request()->routeIs('admin.invoices.*') ? 'true' : 'false' }}"
        class="space-y-1">
        <button @click="open = !open"
            class="w-full flex items-center justify-between gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors duration-300
        {{ request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.subscription-plans.*') || request()->routeIs('admin.invoices.*') ? 'bg-gray-700 text-white' : '' }}">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 2a3 3 0 00-3 3h6a3 3 0 00-3-3z" />
                </svg>
                <span>Subscriptions</span>
            </div>
            <svg :class="{ 'rotate-90': open }" class="w-4 h-4 transition-transform transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <div x-show="open" x-collapse class="pl-6 space-y-1">
            <a href="{{ route('admin.subscriptions.index') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.subscriptions.index') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                All Subscriptions
            </a>
            <a href="{{ route('admin.subscription-plans.index') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Plans
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.invoices.*') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Invoices
            </a>
        </div>
    </div>

    <!-- Profile Approvals -->
    <a href="{{ route('admin.profile-approval.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
        @if (request()->routeIs('admin.profile-approval.*')) bg-gray-700 text-white @endif
        transition-colors duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Profile Approvals</span>
    </a>

    <!-- Settings -->
    <a href="{{ route('admin.settings.index') }}"
        class="flex items-center gap-2 py-2 px-4 rounded-lg hover:bg-gray-700
        @if (request()->routeIs('admin.settings.*')) bg-gray-700 text-white @endif
        transition-colors duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span>Settings</span>
    </a>

    <!-- Exclusive Content Accordion -->
    <div x-data="{ open: false }"
        x-init="open = {{ request()->routeIs('admin.exclusive.*') ? 'true' : 'false' }}"
        class="space-y-1">
        <button @click="open = !open"
            class="w-full flex items-center justify-between gap-2 py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors duration-300
        {{ request()->routeIs('admin.exclusive.*') ? 'bg-gray-700 text-white' : '' }}">
            <div class="flex items-center gap-2">
                <!-- Star/Exclusive Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span>Exclusive Content</span>
            </div>
            <svg :class="{ 'rotate-90': open }" class="w-4 h-4 transition-transform transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <div x-show="open" x-collapse class="pl-6 space-y-1">
            <a href="{{ route('admin.exclusive.moderation') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.exclusive.moderation') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Moderation
            </a>
            <a href="{{ route('admin.exclusive.financial') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.exclusive.financial') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Financial Dashboard
            </a>
            <a href="{{ route('admin.exclusive.settings') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.exclusive.settings') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                Settings
            </a>
        </div>
    </div>

    {{-- <div
        x-data="{ open: {{ request()->routeIs('admin.ads.*') || request()->routeIs('admin.ad.ad-packages.*') ? 'true' : 'false' }} }"
        class="space-y-1"> --}}
        <div x-data="{ open: false }"
            x-init="open = {{ request()->routeIs('admin.ads.*') || request()->routeIs('admin.ad.ad-packages.*') ? 'true' : 'false' }}"
            class="space-y-1">

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
                <a href="{{ route('admin.ads.index') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ads.index') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                    All Ads
                </a>
                <a href="{{ route('admin.ads.pending') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ads.pending') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                    Pending Approval
                </a>
                <a href="{{ route('admin.ad.ad-packages.index') }}" class="block py-2 px-4 rounded-lg hover:bg-gray-700 transition
            {{ request()->routeIs('admin.ad.ad-packages.*') ? 'bg-gray-700 text-white' : 'text-gray-500' }}">
                    Ad Packages
                </a>
            </div>
        </div>

</nav>