<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsAppInvoice;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierController extends Controller
{
    /**
     * Tampilkan stasiun kasir / monitor POS.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'all_active');
        $search = $request->input('search');

        $query = Order::with(['table', 'items.menu']);

        // Search by order_number, customer_name, customer_whatsapp, or table
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_whatsapp', 'like', "%{$search}%")
                    ->orWhereHas('table', function ($t) use ($search) {
                        $t->where('table_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filtering
        if ($statusFilter === 'pending') {
            $query->where('status', 'pending_payment');
        } elseif ($statusFilter === 'paid') {
            $query->where('status', 'paid');
        } elseif ($statusFilter === 'processing') {
            $query->whereIn('status', ['processing', 'ready']);
        } elseif ($statusFilter === 'completed') {
            $query->where('status', 'completed');
        } elseif ($statusFilter === 'all_active') {
            // Pesanan aktif: pending, paid, processing, ready, atau selesai hari ini
            $query->where(function ($q) {
                $q->whereIn('status', ['pending_payment', 'paid', 'processing', 'ready'])
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'completed')
                            ->whereDate('created_at', Carbon::today());
                    });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Statistik kasir hari ini
        $todayPendingCount = Order::where('status', 'pending_payment')->count();
        $todayPaidOrders = Order::whereIn('status', ['paid', 'processing', 'ready', 'completed'])
            ->whereDate('created_at', Carbon::today())
            ->count();
        $todayRevenue = Order::whereIn('status', ['paid', 'processing', 'ready', 'completed'])
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $totalOrdersCount = Order::whereDate('created_at', Carbon::today())->count();

        return view('admin.cashier.index', compact(
            'orders',
            'todayPendingCount',
            'todayPaidOrders',
            'todayRevenue',
            'totalOrdersCount',
            'statusFilter',
            'search'
        ));
    }

    /**
     * Perbarui status pesanan (Lunas / Batal) dari kasir.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:paid,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        // Log to order_status_logs
        $order->statusLogs()->create([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->status === 'paid' && ($order->customer_whatsapp || $order->customer_phone)) {
            SendWhatsAppInvoice::dispatch($order);
        }

        $msg = $request->status === 'paid'
            ? 'Pembayaran pesanan '.$order->order_number.' berhasil diterima (Lunas) & struk otomatis dikirim via WhatsApp.'
            : 'Pesanan '.$order->order_number.' telah dibatalkan.';

        return back()->with('success', $msg);
    }
}
