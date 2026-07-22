@extends('admin.layouts.app')

@section('title', 'Exclusive Content Statistics & Analytics')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Exclusive Content Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Track views, earnings, purchases, and viewer conversions across exclusive content.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" action="{{ route('creator.dashboard.exclusive-content') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Content Type</label>
                <select name="content_type" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="all" {{ $contentType == 'all' ? 'selected' : '' }}>All Content</option>
                    <option value="post" {{ $contentType == 'post' ? 'selected' : '' }}>Posts</option>
                    <option value="reel" {{ $contentType == 'reel' ? 'selected' : '' }}>Reels</option>
                    <option value="video" {{ $contentType == 'video' ? 'selected' : '' }}>Videos</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sort By</label>
                <div class="flex gap-2">
                    <select name="sort_by" class="w-2/3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="earnings" {{ $sortBy == 'earnings' ? 'selected' : '' }}>Earnings</option>
                        <option value="views" {{ $sortBy == 'views' ? 'selected' : '' }}>Views</option>
                        <option value="purchases" {{ $sortBy == 'purchases' ? 'selected' : '' }}>Purchases</option>
                    </select>
                    <button type="submit" class="w-1/3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-4 py-2.5 transition-colors">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Global Stats Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500 flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Global Total Earnings</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">₹{{ number_format($globalStats['global_total_earnings'], 2) }}</p>
            </div>
            <div class="p-3 bg-green-100 text-green-600 rounded-full">
                <i class="fas fa-indian-rupee-sign text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500 flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Global Total Views</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($globalStats['global_total_views']) }}</p>
            </div>
            <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                <i class="fas fa-eye text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500 flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Global Purchases</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($globalStats['global_total_purchases']) }}</p>
            </div>
            <div class="p-3 bg-purple-100 text-purple-600 rounded-full">
                <i class="fas fa-shopping-bag text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-t-4 border-amber-500 flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Active Exclusive Content</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($globalStats['global_total_exclusive_content']) }}</p>
            </div>
            <div class="p-3 bg-amber-100 text-amber-600 rounded-full">
                <i class="fas fa-photo-video text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Spotlight Top Performers -->
    <div class="mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-trophy text-amber-500"></i> Spotlight Highlights
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Most Viewed -->
            <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase px-2.5 py-1 rounded bg-blue-100 text-blue-700">Most Viewed</span>
                </div>
                @if($spotlight['most_viewed'])
                    <div class="flex items-center gap-4">
                        <img src="{{ $spotlight['most_viewed']['thumbnail_url'] ?? asset('images/default-thumbnail.png') }}" class="w-14 h-14 rounded-lg object-cover bg-gray-100" alt="Thumb">
                        <div class="overflow-hidden">
                            <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $spotlight['most_viewed']['caption'] ?? 'Content #' . $spotlight['most_viewed']['id'] }}</h4>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-eye text-blue-500"></i> {{ number_format($spotlight['most_viewed']['views_count']) }} Total Views</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No views recorded yet.</p>
                @endif
            </div>

            <!-- Most Purchased -->
            <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase px-2.5 py-1 rounded bg-purple-100 text-purple-700">Most Purchased</span>
                </div>
                @if($spotlight['most_purchased'])
                    <div class="flex items-center gap-4">
                        <img src="{{ $spotlight['most_purchased']['thumbnail_url'] ?? asset('images/default-thumbnail.png') }}" class="w-14 h-14 rounded-lg object-cover bg-gray-100" alt="Thumb">
                        <div class="overflow-hidden">
                            <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $spotlight['most_purchased']['caption'] ?? 'Content #' . $spotlight['most_purchased']['id'] }}</h4>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-shopping-cart text-purple-500"></i> {{ number_format($spotlight['most_purchased']['purchases_count']) }} Purchases</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No purchases recorded yet.</p>
                @endif
            </div>

            <!-- Highest Earning -->
            <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase px-2.5 py-1 rounded bg-green-100 text-green-700">Highest Earning</span>
                </div>
                @if($spotlight['highest_earning'])
                    <div class="flex items-center gap-4">
                        <img src="{{ $spotlight['highest_earning']['thumbnail_url'] ?? asset('images/default-thumbnail.png') }}" class="w-14 h-14 rounded-lg object-cover bg-gray-100" alt="Thumb">
                        <div class="overflow-hidden">
                            <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $spotlight['highest_earning']['caption'] ?? 'Content #' . $spotlight['highest_earning']['id'] }}</h4>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-indian-rupee-sign text-green-500"></i> ₹{{ number_format($spotlight['highest_earning']['total_earnings'], 2) }} Revenue</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No earnings recorded yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Per-Item Performance Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchases</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distinct Buyers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($exclusiveContent as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item['thumbnail_url'] ?? asset('images/default-thumbnail.png') }}" class="w-10 h-10 rounded-md object-cover bg-gray-100" alt="Thumb">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ Str::limit($item['caption'] ?? 'Exclusive Item #' . $item['id'], 30) }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $item['id'] }} • {{ $item['created_at'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-bold rounded uppercase
                                    @if($item['type'] == 'post') bg-blue-100 text-blue-700
                                    @elseif($item['type'] == 'reel') bg-pink-100 text-pink-700
                                    @else bg-purple-100 text-purple-700 @endif">
                                    {{ $item['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ₹{{ number_format($item['price'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-eye text-blue-500 mr-1"></i> {{ number_format($item['total_views']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-shopping-bag text-purple-500 mr-1"></i> {{ number_format($item['total_purchase_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-users text-amber-500 mr-1"></i> {{ number_format($item['total_purchase_users']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-user-check text-pink-500 mr-1"></i> {{ number_format($item['purchased_user_views']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                ₹{{ number_format($item['total_earnings'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                No exclusive content records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exclusiveContent->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $exclusiveContent->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
