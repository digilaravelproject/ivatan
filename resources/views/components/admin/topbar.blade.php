<div class="flex items-center justify-between bg-gray-800 text-white px-4 py-3 shadow-md"
     x-data="notificationBell()"
     x-init="init()"
>
    <div class="text-lg font-semibold">
        @isset($title)
            {{ $title }}
        @else
            Admin Panel
        @endisset
    </div>

    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <div class="relative">
            <button @click="toggleDropdown()" class="p-2 rounded-lg hover:bg-gray-700 transition-colors duration-300 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="unreadCount > 0"
                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                      class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                ></span>
            </button>

            <!-- Dropdown -->
            <div x-show="open"
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-80 bg-white text-gray-800 rounded-lg shadow-xl border z-50"
                 x-transition
            >
                <div class="p-3 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-sm">Notifications</h3>
                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
                </div>

                <div class="max-h-64 overflow-y-auto">
                    <template x-if="recent.length === 0">
                        <p class="p-4 text-sm text-gray-400 text-center">No notifications</p>
                    </template>

                    <template x-for="n in recent" :key="n.id">
                        <a :href="'{{ route('admin.notifications.show', '') }}/' + n.id"
                           class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0"
                           :class="n.read_at ? '' : 'bg-blue-50'"
                        >
                            <p class="text-sm font-medium" x-text="n.title"></p>
                            <p class="text-xs text-gray-500 truncate" x-text="n.message"></p>
                            <p class="text-[10px] text-gray-400 mt-1" x-text="n.time"></p>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <!-- Profile dropdown -->
        <div class="relative">
            <button id="profileBtn" class="flex items-center gap-2 rounded-lg hover:bg-gray-700 px-2 py-1 transition-colors duration-300">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile"  loading="lazy" class="w-8 h-8 rounded-full object-cover border-2 border-white">
                {{ auth()->user()->name }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

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

    function notificationBell() {
        return {
            open: false,
            unreadCount: 0,
            recent: [],
            init() {
                this.fetchUnreadCount();
                this.fetchRecent();
            },
            toggleDropdown() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchUnreadCount();
                    this.fetchRecent();
                }
            },
            fetchUnreadCount() {
                axios.get('/admin/notifications/unread-count')
                    .then(res => { this.unreadCount = res.data.count ?? 0; })
                    .catch(() => {});
            },
            fetchRecent() {
                axios.get('/admin/notifications/recent')
                    .then(res => { this.recent = res.data.notifications ?? []; })
                    .catch(() => {});
            },
        };
    }
</script>
