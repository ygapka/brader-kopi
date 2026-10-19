<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\Table;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerOrderController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Tampilkan halaman katalog menu untuk meja tertentu.
     */
    public function index(string $token)
    {
        $table = Table::where('token', $token)->first();

        if (! $table || ! $table->is_active) {
            return view('errors.table_inactive', [
                'token' => $token,
                'table' => $table,
            ]);
        }

        // Simpan meja ke session
        session(['table_id' => $table->id]);

        // Ambil kategori beserta menu yang aktif dan variannya
        $categories = Category::with(['menus' => function ($query) {
            $query->where('is_available', true)->with('variants');
        }])->get();

        return view('customer.menu', compact('table', 'categories', 'token'));
    }

    /**
     * Proses checkout pesanan.
     */
    public function checkout(Request $request, string $token)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_whatsapp' => 'required|string|max:20',
            'items' => 'required|json',
        ]);

        $table = Table::where('token', $token)->firstOrFail();

        if (! $table->is_active) {
            return back()->with('error', 'Meja tidak aktif.');
        }

        $items = json_decode($request->input('items'), true);
        if (empty($items)) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);

                // Base price
                $price = $menu->price;

                // Add variant extra price if applicable
                $variantName = $item['variant_name'] ?? null;
                if ($variantName) {
                    $variant = MenuVariant::where('menu_id', $menu->id)->where('name', $variantName)->first();
                    if ($variant) {
                        $price += $variant->extra_price;
                    }
                }

                $quantity = (int) $item['quantity'];
                if ($quantity < 1) {
                    continue; // Skip invalid quantity
                }

                $totalAmount += ($price * $quantity);

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'variant_name' => $variantName,
                    'quantity' => $quantity,
                    'price' => $price,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            if (empty($orderItemsData)) {
                throw new \Exception('Keranjang tidak valid.');
            }

            // Generate unique Order Number
            do {
                $orderNumber = 'ORD-'.strtoupper(Str::random(8));
            } while (Order::where('order_number', $orderNumber)->exists());

            // Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'table_id' => $table->id,
                'customer_name' => $request->input('customer_name'),
                'customer_whatsapp' => $request->input('customer_whatsapp'),
                'total_amount' => $totalAmount,
                'status' => 'pending_payment',
            ]);

            // Create Order Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            DB::commit();

            // Generate Midtrans Snap Token
            $snapToken = $this->midtransService->getSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }

            return redirect()->route('order.success', ['token' => $token, 'order' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses pesanan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan halaman sukses.
     */
    public function success(string $token, Order $order)
    {
        // Pastikan pesanan milik meja ini
        $table = Table::where('token', $token)->firstOrFail();
        if ($order->table_id !== $table->id) {
            abort(404);
        }

        $order->loadMissing(['items.menu', 'table']);

        // Jika masih pending_payment, periksa apakah di Midtrans sudah dibayar
        if ($order->status === 'pending_payment') {
            $this->midtransService->checkAndUpdateStatus($order);
            $order->refresh();
        }

        // Jika snap_token belum dibuat dan pesanan belum dibayar, coba generate
        if (empty($order->snap_token) && $order->status === 'pending_payment') {
            $snapToken = $this->midtransService->getSnapToken($order);
            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        }

        $snapToken = $order->snap_token;

        return view('customer.success', compact('order', 'table', 'token', 'snapToken'));
    }

    /**
     * Cek status pembayaran real-time via AJAX polling.
     */
    public function checkStatus(string $token, Order $order): JsonResponse
    {
        $table = Table::where('token', $token)->firstOrFail();
        if ($order->table_id !== $table->id) {
            return response()->json(['error' => 'Meja tidak valid'], 403);
        }

        $status = $this->midtransService->checkAndUpdateStatus($order);

        return response()->json([
            'status' => $status,
            'is_paid' => $status === 'paid',
        ]);
    }
}
