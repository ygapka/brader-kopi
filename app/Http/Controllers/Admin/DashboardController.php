<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Akses dashboard hanya untuk Administrator.');
        }

        $totalTables = Table::count();
        $activeTables = Table::where('is_active', true)->count();
        $totalCategories = Category::count();
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->count();

        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

        $paidOrdersQuery = Order::whereIn('status', ['paid', 'completed']);
        $totalRevenue = (clone $paidOrdersQuery)->sum('total_amount');
        $todayRevenue = (clone $paidOrdersQuery)->whereDate('created_at', Carbon::today())->sum('total_amount');

        $paidOrdersCount = (clone $paidOrdersQuery)->count();
        $averageOrderValue = $paidOrdersCount > 0 ? ($totalRevenue / $paidOrdersCount) : 0;

        // 7-day sales trend data for simple interactive chart
        $salesChartData = [];
        $maxDailyRevenue = 1000;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $date->translatedFormat('D, d M');
            $shortLabel = $date->translatedFormat('D');

            $dayRevenue = (float) Order::whereIn('status', ['paid', 'completed'])
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');

            $dayCount = Order::whereIn('status', ['paid', 'completed'])
                ->whereDate('created_at', $date->toDateString())
                ->count();

            if ($dayRevenue > $maxDailyRevenue) {
                $maxDailyRevenue = $dayRevenue;
            }

            $salesChartData[] = [
                'date' => $date->toDateString(),
                'label' => $dayLabel,
                'short' => $shortLabel,
                'revenue' => $dayRevenue,
                'count' => $dayCount,
            ];
        }

        // Recent orders with audit logs (status changes)
        $recentOrders = Order::with(['table', 'items.menu', 'statusLogs.updater'])
            ->latest()
            ->take(8)
            ->get();

        $recentTables = Table::latest()->take(5)->get();
        $recentMenus = Menu::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalTables',
            'activeTables',
            'totalCategories',
            'totalMenus',
            'availableMenus',
            'totalOrders',
            'todayOrders',
            'totalRevenue',
            'todayRevenue',
            'averageOrderValue',
            'salesChartData',
            'maxDailyRevenue',
            'recentOrders',
            'recentTables',
            'recentMenus'
        ));
    }
}
