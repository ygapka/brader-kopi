<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerController extends Controller
{
    /**
     * Tampilkan Portal Eksekutif & Ringkasan Performa Bisnis Pemilik (Owner).
     */
    public function index(Request $request): View
    {
        // Hanya owner dan administrator yang dapat mengakses halaman owner
        if (! in_array(auth()->user()?->role, ['owner', 'admin'])) {
            abort(403, 'Akses terbatas. Halaman ini hanya untuk Pemilik (Owner) dan Administrator.');
        }

        $now = Carbon::now();
        $today = Carbon::today();

        // 1. Metrik Pendapatan Utama
        $successfulStatuses = ['paid', 'processing', 'ready', 'completed'];

        $totalRevenue = (float) Order::whereIn('status', $successfulStatuses)->sum('total_amount');
        $totalPaidOrders = Order::whereIn('status', $successfulStatuses)->count();
        $averageOrderValue = $totalPaidOrders > 0 ? ($totalRevenue / $totalPaidOrders) : 0;

        $todayRevenue = (float) Order::whereIn('status', $successfulStatuses)
            ->whereDate('created_at', $today)
            ->sum('total_amount');
        $todayOrders = Order::whereIn('status', $successfulStatuses)
            ->whereDate('created_at', $today)
            ->count();

        $thisWeekRevenue = (float) Order::whereIn('status', $successfulStatuses)
            ->whereBetween('created_at', [$today->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()])
            ->sum('total_amount');

        $thisMonthRevenue = (float) Order::whereIn('status', $successfulStatuses)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total_amount');

        $lastMonthDate = $now->copy()->subMonth();
        $lastMonthRevenue = (float) Order::whereIn('status', $successfulStatuses)
            ->whereYear('created_at', $lastMonthDate->year)
            ->whereMonth('created_at', $lastMonthDate->month)
            ->sum('total_amount');

        $monthlyGrowth = null;
        if ($lastMonthRevenue > 0) {
            $monthlyGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // 2. Tren Penjualan 14 Hari Terakhir
        $salesTrend = [];
        $maxDailyRevenue = 50000; // nilai minimum batas skala atas (Rp 50.000)

        for ($i = 13; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dayRevenue = (float) Order::whereIn('status', $successfulStatuses)
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');

            $dayCount = Order::whereIn('status', $successfulStatuses)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            if ($dayRevenue > $maxDailyRevenue) {
                $maxDailyRevenue = $dayRevenue;
            }

            $salesTrend[] = [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('d M'),
                'day' => $date->translatedFormat('D'),
                'revenue' => $dayRevenue,
                'count' => $dayCount,
                'is_today' => $date->isToday(),
            ];
        }

        // Hitung persentase tinggi bar grafik
        foreach ($salesTrend as &$point) {
            $point['height_pct'] = $maxDailyRevenue > 0 ? round(($point['revenue'] / $maxDailyRevenue) * 100) : 0;
            // Minimal 4% agar bar tetap tampak secara visual jika 0
            if ($point['height_pct'] < 4 && $point['revenue'] > 0) {
                $point['height_pct'] = 4;
            }
        }
        unset($point);

        // Hari dengan omzet tertinggi dalam rentang 14 hari
        $peakTrendDay = collect($salesTrend)->sortByDesc('revenue')->first();

        // 3. Distribusi Metode Pembayaran (QRIS Midtrans vs Tunai Kasir)
        $settledPayments = Payment::where('status', 'settlement')->get();
        $qrisRevenue = (float) $settledPayments->sum('gross_amount');
        $qrisCount = $settledPayments->count();

        // Pesanan lunas yang dibayarkan tunai
        $onlineOrderIds = $settledPayments->pluck('order_id')->toArray();
        $cashRevenue = (float) Order::whereIn('status', $successfulStatuses)
            ->whereNotIn('id', $onlineOrderIds)
            ->sum('total_amount');
        $cashCount = Order::whereIn('status', $successfulStatuses)
            ->whereNotIn('id', $onlineOrderIds)
            ->count();

        $combinedPaymentRevenue = $qrisRevenue + $cashRevenue;
        $qrisPercentage = $combinedPaymentRevenue > 0 ? round(($qrisRevenue / $combinedPaymentRevenue) * 100) : 0;
        $cashPercentage = $combinedPaymentRevenue > 0 ? (100 - $qrisPercentage) : 0;

        // 4. Top 5 Menu Paling Laris & Kontributor Omzet Tertinggi
        $topMenus = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->leftJoin('categories', 'menus.category_id', '=', 'categories.id')
            ->whereIn('orders.status', $successfulStatuses)
            ->selectRaw('menus.id, menus.name, menus.image, categories.name as category_name, SUM(order_items.quantity) as total_qty, SUM(order_items.price * order_items.quantity) as total_sales')
            ->groupBy('menus.id', 'menus.name', 'menus.image', 'categories.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        foreach ($topMenus as $menuItem) {
            if ($menuItem->image) {
                $menuItem->image_url = (str_starts_with($menuItem->image, 'http://') || str_starts_with($menuItem->image, 'https://'))
                    ? $menuItem->image
                    : asset('storage/'.$menuItem->image);
            } else {
                $menuItem->image_url = null;
            }
        }

        // 5. Kontribusi Penjualan per Kategori Menu
        $categorySales = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->whereIn('orders.status', $successfulStatuses)
            ->selectRaw('categories.name as category_name, SUM(order_items.quantity) as total_qty, SUM(order_items.price * order_items.quantity) as total_sales')
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $totalCategorySalesSum = $categorySales->sum('total_sales');
        foreach ($categorySales as $cat) {
            $cat->percentage = $totalCategorySalesSum > 0 ? round(($cat->total_sales / $totalCategorySalesSum) * 100) : 0;
        }

        // 6. Analisis Jam Sibuk Kafe (Peak Operating Hours - 30 Hari Terakhir)
        $past30DaysOrders = Order::whereIn('status', $successfulStatuses)
            ->where('created_at', '>=', $today->copy()->subDays(30)->startOfDay())
            ->get(['id', 'total_amount', 'created_at']);

        $timeBlocks = [
            'morning' => ['label' => 'Pagi - Siang (08:00 - 13:59)', 'icon' => 'fa-sun', 'count' => 0, 'revenue' => 0],
            'afternoon' => ['label' => 'Sore Santai (14:00 - 17:59)', 'icon' => 'fa-cloud-sun', 'count' => 0, 'revenue' => 0],
            'evening' => ['label' => 'Malam / Prime (18:00 - 23:59)', 'icon' => 'fa-moon', 'count' => 0, 'revenue' => 0],
        ];

        foreach ($past30DaysOrders as $order) {
            $hour = (int) $order->created_at->format('H');
            if ($hour >= 8 && $hour < 14) {
                $timeBlocks['morning']['count']++;
                $timeBlocks['morning']['revenue'] += (float) $order->total_amount;
            } elseif ($hour >= 14 && $hour < 18) {
                $timeBlocks['afternoon']['count']++;
                $timeBlocks['afternoon']['revenue'] += (float) $order->total_amount;
            } else {
                $timeBlocks['evening']['count']++;
                $timeBlocks['evening']['revenue'] += (float) $order->total_amount;
            }
        }

        // 7. Okupansi & Meja Favorit Pelanggan
        $totalTables = Table::count();
        $activeTables = Table::where('is_active', true)->count();

        $topTables = Order::query()
            ->join('tables', 'orders.table_id', '=', 'tables.id')
            ->whereIn('orders.status', $successfulStatuses)
            ->selectRaw('tables.id, tables.table_number, COUNT(orders.id) as total_orders, SUM(orders.total_amount) as total_spent')
            ->groupBy('tables.id', 'tables.table_number')
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get();

        // 8. Transaksi Terbaru Nilai Tertinggi (High-Value VIP Orders)
        $recentHighValueOrders = Order::with(['table', 'items.menu'])
            ->whereIn('status', $successfulStatuses)
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return view('admin.owner.index', compact(
            'totalRevenue',
            'totalPaidOrders',
            'averageOrderValue',
            'todayRevenue',
            'todayOrders',
            'thisWeekRevenue',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'monthlyGrowth',
            'salesTrend',
            'peakTrendDay',
            'qrisRevenue',
            'qrisCount',
            'cashRevenue',
            'cashCount',
            'qrisPercentage',
            'cashPercentage',
            'combinedPaymentRevenue',
            'topMenus',
            'categorySales',
            'timeBlocks',
            'totalTables',
            'activeTables',
            'topTables',
            'recentHighValueOrders'
        ));
    }
}
