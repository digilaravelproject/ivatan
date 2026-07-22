<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creator Exclusive Content Analytics Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --card-border: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
            --accent-green: #10b981;
            --accent-blue: #3b82f6;
            --accent-amber: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 24px;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .dashboard-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dashboard-title p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        /* Filters Bar */
        .filter-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 18px 24px;
            margin-bottom: 28px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .filter-select, .filter-input {
            background: #0f172a;
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .filter-select:focus, .filter-input:focus {
            border-color: var(--accent-purple);
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
        }

        .btn-apply {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn-apply:hover {
            transform: translateY(-2px);
        }

        /* Overview Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(139, 92, 246, 0.4);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.earnings::before { background: var(--accent-green); }
        .stat-card.views::before { background: var(--accent-blue); }
        .stat-card.purchases::before { background: var(--accent-purple); }
        .stat-card.items::before { background: var(--accent-amber); }

        .stat-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-card .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .earnings .icon-box { background: rgba(16, 185, 129, 0.15); color: var(--accent-green); }
        .views .icon-box { background: rgba(59, 130, 246, 0.15); color: var(--accent-blue); }
        .purchases .icon-box { background: rgba(139, 92, 246, 0.15); color: var(--accent-purple); }
        .items .icon-box { background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-title {
            color: var(--text-secondary);
            font-size: 0.88rem;
            font-weight: 500;
        }

        /* Spotlight Section */
        .spotlight-section {
            margin-bottom: 36px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .spotlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }

        .spotlight-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            gap: 16px;
            align-items: center;
            position: relative;
        }

        .spotlight-badge {
            position: absolute;
            top: 12px;
            right: 14px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .spotlight-badge.viewed { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .spotlight-badge.purchased { background: rgba(139, 92, 246, 0.2); color: #c084fc; }
        .spotlight-badge.earning { background: rgba(16, 185, 129, 0.2); color: #34d399; }

        .spotlight-thumb {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            background: #0f172a;
            flex-shrink: 0;
        }

        .spotlight-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .spotlight-metrics {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Interactive Data Table */
        .table-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #0f172a;
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border-bottom: 1px solid var(--card-border);
        }

        th a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
            font-size: 0.9rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .item-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .item-thumb {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: #0f172a;
        }

        .badge-type {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .type-post { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .type-reel { background: rgba(236, 72, 153, 0.2); color: #f472b6; }
        .type-video { background: rgba(139, 92, 246, 0.2); color: #c084fc; }

        /* Pagination */
        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1><i class="fa-solid font-awesome-logo-full"></i> Exclusive Content Performance</h1>
            <p>Track views, earnings, purchases, and viewer conversions for your exclusive content.</p>
        </div>
    </div>

    <!-- Date Range & Category Filter -->
    <form method="GET" action="{{ url('/creator/dashboard/exclusive-content') }}" class="filter-card">
        <div class="filter-group">
            <span class="filter-label"><i class="fa-regular fa-calendar"></i> Date Range</span>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="filter-input">
            <span style="color: var(--text-secondary);">-</span>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="filter-input">
        </div>

        <div class="filter-group">
            <span class="filter-label"><i class="fa-solid fa-layer-group"></i> Content Type</span>
            <select name="content_type" class="filter-select">
                <option value="all" {{ $contentType == 'all' ? 'selected' : '' }}>All Content</option>
                <option value="post" {{ $contentType == 'post' ? 'selected' : '' }}>Posts</option>
                <option value="reel" {{ $contentType == 'reel' ? 'selected' : '' }}>Reels</option>
                <option value="video" {{ $contentType == 'video' ? 'selected' : '' }}>Videos</option>
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label"><i class="fa-solid fa-arrow-down-short-wide"></i> Sort By</span>
            <select name="sort_by" class="filter-select">
                <option value="earnings" {{ $sortBy == 'earnings' ? 'selected' : '' }}>Total Earnings</option>
                <option value="views" {{ $sortBy == 'views' ? 'selected' : '' }}>Total Views</option>
                <option value="purchases" {{ $sortBy == 'purchases' ? 'selected' : '' }}>Total Purchases</option>
            </select>
            <select name="order" class="filter-select">
                <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>

        <button type="submit" class="btn-apply"><i class="fa-solid fa-filter"></i> Apply Filters</button>
    </form>

    <!-- Global Overview Cards -->
    <div class="cards-grid">
        <div class="stat-card earnings">
            <div class="card-header">
                <span class="stat-title">Global Total Earnings</span>
                <div class="icon-box"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            </div>
            <div class="stat-value">₹{{ number_format($globalStats['global_total_earnings'], 2) }}</div>
        </div>

        <div class="stat-card views">
            <div class="card-header">
                <span class="stat-title">Global Total Views</span>
                <div class="icon-box"><i class="fa-solid fa-eye"></i></div>
            </div>
            <div class="stat-value">{{ number_format($globalStats['global_total_views']) }}</div>
        </div>

        <div class="stat-card purchases">
            <div class="card-header">
                <span class="stat-title">Global Total Purchases</span>
                <div class="icon-box"><div class="icon-box"><i class="fa-solid fa-bag-shopping"></i></div></div>
            </div>
            <div class="stat-value">{{ number_format($globalStats['global_total_purchases']) }}</div>
        </div>

        <div class="stat-card items">
            <div class="card-header">
                <span class="stat-title">Active Exclusive Content</span>
                <div class="icon-box"><i class="fa-solid fa-photo-film"></i></div>
            </div>
            <div class="stat-value">{{ number_format($globalStats['global_total_exclusive_content']) }}</div>
        </div>
    </div>

    <!-- Spotlight Top Performers -->
    <div class="spotlight-section">
        <h2 class="section-title"><i class="fa-solid fa-trophy" style="color: var(--accent-amber);"></i> Spotlight Highlights</h2>
        <div class="spotlight-grid">
            <!-- Most Viewed -->
            <div class="spotlight-card">
                <span class="spotlight-badge viewed">Most Viewed</span>
                @if($spotlight['most_viewed'])
                    <img src="{{ $spotlight['most_viewed']['thumbnail_url'] ?? 'https://via.placeholder.com/150' }}" class="spotlight-thumb" alt="Thumbnail">
                    <div class="spotlight-info">
                        <h4>{{ $spotlight['most_viewed']['caption'] ?? 'Exclusive Item #' . $spotlight['most_viewed']['id'] }}</h4>
                        <div class="spotlight-metrics"><i class="fa-solid fa-eye"></i> {{ number_format($spotlight['most_viewed']['views_count']) }} Total Views</div>
                    </div>
                @else
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">No views recorded yet.</p>
                @endif
            </div>

            <!-- Most Purchased -->
            <div class="spotlight-card">
                <span class="spotlight-badge purchased">Most Purchased</span>
                @if($spotlight['most_purchased'])
                    <img src="{{ $spotlight['most_purchased']['thumbnail_url'] ?? 'https://via.placeholder.com/150' }}" class="spotlight-thumb" alt="Thumbnail">
                    <div class="spotlight-info">
                        <h4>{{ $spotlight['most_purchased']['caption'] ?? 'Exclusive Item #' . $spotlight['most_purchased']['id'] }}</h4>
                        <div class="spotlight-metrics"><i class="fa-solid fa-cart-shopping"></i> {{ number_format($spotlight['most_purchased']['purchases_count']) }} Purchases</div>
                    </div>
                @else
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">No purchases recorded yet.</p>
                @endif
            </div>

            <!-- Highest Earning -->
            <div class="spotlight-card">
                <span class="spotlight-badge earning">Highest Earning</span>
                @if($spotlight['highest_earning'])
                    <img src="{{ $spotlight['highest_earning']['thumbnail_url'] ?? 'https://via.placeholder.com/150' }}" class="spotlight-thumb" alt="Thumbnail">
                    <div class="spotlight-info">
                        <h4>{{ $spotlight['highest_earning']['caption'] ?? 'Exclusive Item #' . $spotlight['highest_earning']['id'] }}</h4>
                        <div class="spotlight-metrics"><i class="fa-solid fa-indian-rupee-sign"></i> ₹{{ number_format($spotlight['highest_earning']['total_earnings'], 2) }} Earned</div>
                    </div>
                @else
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">No earnings recorded yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Interactive Data Table -->
    <div class="table-card">
        <h2 class="section-title"><i class="fa-solid fa-table-list"></i> Per-Item Performance Breakdowns</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Content Item</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Total Views</th>
                        <th>Total Purchases</th>
                        <th>Distinct Buyers</th>
                        <th>Purchased User Views</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exclusiveContent as $item)
                        <tr>
                            <td>
                                <div class="item-cell">
                                    <img src="{{ $item['thumbnail_url'] ?? 'https://via.placeholder.com/100' }}" class="item-thumb" alt="Thumb">
                                    <div>
                                        <div style="font-weight: 600;">{{ Str::limit($item['caption'] ?? 'Exclusive Content #' . $item['id'], 35) }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-secondary);">ID: {{ $item['id'] }} • Created: {{ $item['created_at'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-type type-{{ $item['type'] }}">{{ $item['type'] }}</span></td>
                            <td style="font-weight: 600;">₹{{ number_format($item['price'], 2) }}</td>
                            <td><i class="fa-solid fa-eye" style="color: var(--accent-blue);"></i> {{ number_format($item['total_views']) }}</td>
                            <td><i class="fa-solid fa-bag-shopping" style="color: var(--accent-purple);"></i> {{ number_format($item['total_purchase_count']) }}</td>
                            <td><i class="fa-solid fa-users" style="color: var(--accent-amber);"></i> {{ number_format($item['total_purchase_users']) }}</td>
                            <td><i class="fa-solid fa-user-check" style="color: var(--accent-pink);"></i> {{ number_format($item['purchased_user_views']) }}</td>
                            <td style="font-weight: 700; color: var(--accent-green);">₹{{ number_format($item['total_earnings'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                                No exclusive content found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exclusiveContent->hasPages())
            <div class="pagination-wrapper">
                {{ $exclusiveContent->links() }}
            </div>
        @endif
    </div>
</div>

</body>
</html>
