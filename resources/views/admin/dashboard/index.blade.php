@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="mx-auto space-y-6 max-w-7xl">
        <!-- Summary cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6">
            <!-- Users Card -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Users</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd" d="M10 4a4 4 0 100 8 4 4 0 000-8zM2 16a8 8 0 1116 0H2z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['users_total'] ?? 0 }}</div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Active: {{ $summary['users_active'] ?? 0 }}</span>
                    <span>Blocked: {{ $summary['users_blocked'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Posts Card -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Posts</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['posts'] ?? 0 }}</div>
            </div>

            <!-- Stories -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Stories</span>
                    <!-- Play Circle Icon (Better represents "stories") -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.752 11.168l-4.596-2.65A1 1 0 009 9.36v5.278a1 1 0 001.156.987l4.596-1.3a1 1 0 00.748-.967V12.13a1 1 0 00-.748-.962z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['stories_total'] ?? 0 }}</div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Active: {{ $summary['stories_active'] ?? 0 }}</span>
                    <span>Archive: {{ $summary['stories_archived'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Reels Card -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Reels</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke="none" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 8l6 4-6 4V8z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['reels'] ?? 0 }}</div>
            </div>

            <!-- Products Card -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Products</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v18H3V3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v18" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v18" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $summary['products'] ?? 0 }}</div>
            </div>

            <!-- Orders Card -->
            <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Orders</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v6H3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18v12H3z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $summary['orders'] ?? 0 }}</div>
        </div>

        <!-- Jobs Card -->
        <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Jobs</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-500" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V4a4 4 0 00-8 0v3" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10h14l-1 9H6l-1-9z" />
            </svg>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ $summary['jobs'] ?? 0 }}</div>
    </div>
    <!-- Reports-->
    <div class="flex flex-col gap-2 p-6 transition bg-white shadow-md rounded-xl hover:shadow-lg">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Reports Pending</span>
            <!-- Play Circle Icon (Better represents "stories") -->
          📄
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ $summary['reports_pending'] ?? 0 }}</div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>Report Resolved: {{ $summary['reports_resolved'] ?? 0 }}</span>
        </div>
    </div>


        </div>


        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="p-4 bg-white rounded shadow">
                <h3 class="mb-2 font-semibold">Users (last 7 days)</h3>
                <canvas id="usersChart" width="400" height="200"></canvas>
            </div>
            <div class="p-4 bg-white rounded shadow">
                <h3 class="mb-2 font-semibold">Posts (last 7 days)</h3>
                <canvas id="postsChart" width="400" height="200"></canvas>
            </div>
            <div class="p-4 bg-white rounded shadow">
                <h3 class="mb-2 font-semibold">Reels (last 7 days)</h3>
                <canvas id="reelsChart" width="400" height="200"></canvas>
            </div>
            <div class="p-4 bg-white rounded shadow">
                <h3 class="mb-2 font-semibold">Products (last 7 days)</h3>
                <canvas id="productsChart" width="400" height="200"></canvas>
            </div>
        </div>

        {{-- Recents Activity --}}
        <x-admin.module.recent-activity url="{{ url('/admin/dashboard/activity') }}" />


    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        async function fetchChart(type, days = 7) {
            const res = await fetch("{{ url('/admin/dashboard/chart') }}/" + type + "/" + days);
            return res.json();
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // Users chart
            const usersResp = await fetchChart('users', 7);
            const ctxUsers = document.getElementById('usersChart').getContext('2d');
            new Chart(ctxUsers, {
                type: 'line',
                data: {
                    labels: usersResp.labels,
                    datasets: [{
                        label: 'New users',
                        data: usersResp.data,
                        fill: true,
                        tension: 0.3,
                        backgroundColor: 'rgba(99,102,241,0.15)',
                        borderColor: 'rgb(99,102,241)'
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Posts chart
            const postsResp = await fetchChart('user_posts', 7);
            const ctxPosts = document.getElementById('postsChart').getContext('2d');
            new Chart(ctxPosts, {
                type: 'bar',
                data: {
                    labels: postsResp.labels,
                    datasets: [{
                        label: 'Posts',
                        data: postsResp.data,
                        backgroundColor: 'rgba(16,185,129,0.7)'
                    }]
                },
                options: {
                    responsive: true
                }
            });
            // Reels chart
            const reelsResp = await fetchChart('reels', 7);
            const ctxReels = document.getElementById('reelsChart').getContext('2d');
            new Chart(ctxReels, {
                type: 'line',
                data: {
                    labels: reelsResp.labels,
                    datasets: [{
                        label: 'Reels',
                        data: reelsResp.data,
                        fill: true,
                        tension: 0.3,
                        backgroundColor: 'rgba(16,185,129,0.7)',
                        borderColor: 'rgb(99,102,241)'
                    }]
                },
                options: {
                    responsive: true
                }
            });
            // Products chart
            const productsResp = await fetchChart('user_products', 7);
            const ctxProducts = document.getElementById('productsChart').getContext('2d');
            new Chart(ctxProducts, {
                type: 'line',
                data: {
                    labels: productsResp.labels,
                    datasets: [{
                        label: 'Products',
                        data: productsResp.data,
                        fill: true,
                        tension: 0.3,
                        backgroundColor: 'rgba(16,185,129,0.7)',
                        borderColor: 'rgb(99,102,241)'
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
    </script>






@endsection
