<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
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
        $totalRevenue = Order::whereIn('status', ['paid', 'completed'])->sum('total_amount');

        $recentTables = Table::latest()->take(5)->get();
        $recentMenus = Menu::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalTables',
            'activeTables',
            'totalCategories',
            'totalMenus',
            'availableMenus',
            'totalOrders',
            'totalRevenue',
            'recentTables',
            'recentMenus'
        ));
    }
}
