<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    /**
     * Tampilkan Kitchen Display System (KDS) untuk Barista & Dapur.
     */
    public function index(): View
    {
        // KDS menampilkan pesanan paid (masuk), processing (diproses), ready & completed terkini (selesai)
        $orders = Order::with(['table', 'items.menu'])
            ->where(function ($query) {
                $query->whereIn('status', ['paid', 'processing', 'ready'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'completed')
                            ->where('updated_at', '>=', now()->subHours(4));
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.kitchen.index', compact('orders'));
    }

    /**
     * Update status pesanan dari antarmuka KDS.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processing,ready,completed',
        ]);

        $order->update(['status' => $request->status]);

        // Log ke order_status_logs
        $order->statusLogs()->create([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan '.$order->order_number.' berhasil diubah ke '.$request->status,
                'order_id' => $order->id,
                'status' => $order->status,
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
