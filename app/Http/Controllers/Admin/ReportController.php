<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = Order::with('table')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->where('status', 'completed');

        $totalRevenue = (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();
        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('admin.reports.index', compact('orders', 'totalRevenue', 'totalOrders', 'startDate', 'endDate'));
    }
}
