<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Story;
use App\Models\Product;
use App\Models\Order;
use App\Models\Job;
use App\Models\Report;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Admin dashboard view
    public function index()
    {
        $summary = $this->getSummaryCounts();
        return view('admin.dashboard', ['summary' => $summary]);
    }

    // API: summary counts (JSON)
    public function summary()
    {
        return response()->json($this->getSummaryCounts());
    }

    protected function getSummaryCounts(): array
    {
        // check tables exist before querying
        $u = Schema::hasTable('users') ? User::count() : 0;
        // $activeUsers = (Schema::hasTable('users') ? User::where('is_blocked', 0)->count() : 0);
        if (Schema::hasTable('users')) {
            $activeUsers = User::where('is_blocked', 0)
                ->where('status', 'active')
                ->count();

            $inactiveUsers = User::where('status', 'inactive')->count();

            $blockedUsers = User::where('is_blocked', 1)->count();
        } else {
            $activeUsers = $inactiveUsers = $blockedUsers = 0;
        }


        $posts = Schema::hasTable('posts') ? Post::count() : 0;
        $reels = Schema::hasTable('reels') ? Reel::count() : 0;
        $storiesActive = Schema::hasTable('stories') ? Story::where('expires_at', '>', now())->count() : 0;
        $storiesExpired = Schema::hasTable('stories') ? Story::where('expires_at', '<=', now())->count() : 0;
        $products = Schema::hasTable('products') ? Product::count() : 0;
        $orders = Schema::hasTable('orders') ? Order::count() : 0;
        $jobs = Schema::hasTable('user_jobs') ? Job::count() : 0;
        $reportsPending = Schema::hasTable('reports') ? Report::where('status', 'pending')->count() : 0;
        $reportsResolved = Schema::hasTable('reports') ? Report::where('status', 'resolved')->count() : 0;

        return [
            'users_total' => $u,
            'users_active' => $activeUsers,
            'users_blocked' => $blockedUsers,
            'users_inactive' => $inactiveUsers,
            'posts' => $posts,
            'reels' => $reels,
            'stories_active' => $storiesActive,
            'stories_expired' => $storiesExpired,
            'products' => $products,
            'orders' => $orders,
            'jobs' => $jobs,
            'reports_pending' => $reportsPending,
            'reports_resolved' => $reportsResolved,
        ];
    }

    // Chart endpoint: /admin/dashboard/chart/{type}/{days?}
    public function chart(Request $request, $type, $days = 7)
    {
        $days = (int) $days;
        $days = max(1, min($days, 365)); // limit
        $since = now()->subDays($days - 1)->startOfDay();

        $mapping = [
            'users' => 'users',
            'posts' => 'posts',
            'reels' => 'reels',
            'orders' => 'orders',
            // add more mapping if needed
        ];

        if (!isset($mapping[$type]) || !Schema::hasTable($mapping[$type])) {
            return response()->json(['labels' => [], 'data' => []]);
        }

        $table = $mapping[$type];

        $rows = DB::table($table)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // prepare labels (each day) and data (fill zeros)
        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $since->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $since->copy()->addDays($i)->format('M j'); // nicer label
            $data[] = isset($rows[$d]) ? (int)$rows[$d] : 0;
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
