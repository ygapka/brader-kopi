<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppInvoice;
use App\Models\Order;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        // Monitor all active orders (pending, paid, processing, ready)
        // Usually, completed/cancelled are just for history, but we can show recent ones too.
        // Let's show today's orders or active orders.
        $orders = Order::with('table', 'items.menu')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orWhereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.cashier.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:paid,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Log to order_status_logs
        $order->statusLogs()->create([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->status === 'paid' && ($order->customer_whatsapp || $order->customer_phone)) {
            SendWhatsAppInvoice::dispatch($order);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi '.$request->status);
    }
}
