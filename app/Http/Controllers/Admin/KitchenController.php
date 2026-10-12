<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        // Kitchen only sees paid, processing, ready orders.
        $orders = Order::with('table', 'items.menu')
            ->whereIn('status', ['paid', 'processing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.kitchen.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:processing,ready,completed',
        ]);

        $order->update(['status' => $request->status]);

        // Log to order_status_logs
        $order->statusLogs()->create([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
