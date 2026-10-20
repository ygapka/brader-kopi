<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan dengan filter tanggal dan status.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $status = $request->input('status', 'all_paid'); // default: semua transaksi yang berhasil lunas

        $query = Order::with(['table', 'items.menu'])
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);

        // Filter status pesanan
        if ($status === 'all_paid') {
            // Menampilkan semua transaksi yang sudah dibayar (baik baru bayar, diproses, siap, maupun selesai)
            $query->whereIn('status', ['paid', 'processing', 'ready', 'completed']);
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'paid') {
            $query->where('status', 'paid');
        } elseif ($status === 'processing') {
            $query->whereIn('status', ['processing', 'ready']);
        } elseif ($status === 'pending_payment') {
            $query->where('status', 'pending_payment');
        }
        // Jika $status === 'all', tidak dibatasi statusnya

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Total pendapatan dihitung dari transaksi yang sudah lunas
        $totalRevenue = $orders->whereIn('status', ['paid', 'processing', 'ready', 'completed'])->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        return view('admin.reports.index', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'averageOrderValue',
            'startDate',
            'endDate',
            'status'
        ));
    }
}
