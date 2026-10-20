@extends('layouts.admin')

@section('title', 'Stasiun Kasir & POS Monitor')

@section('content')
<div x-data="cashierStation()" class="space-y-6">
    
    <!-- Top Bar Control -->
    <div class="bg-white rounded-3xl p-5 border border-cream-200/90 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-2xl font-bold shadow-xs">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-espresso-950 tracking-tight">Stasiun Kasir & Pembayaran</h2>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        ONLINE
                    </span>
                </div>
                <p class="text-xs text-espresso-600 mt-0.5">Kelola pembayaran tunai meja, validasi transaksi pelanggan, dan cetak struk pesanan.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <button type="button" 
                    onclick="window.location.reload()" 
                    class="px-4 py-2.5 rounded-xl bg-espresso-950 text-white hover:bg-espresso-900 active:scale-95 text-xs font-extrabold shadow-sm flex items-center gap-2 cursor-pointer transition">
                <i class="fa-solid fa-rotate-right"></i>
                <span>Segarkan Data</span>
            </button>
        </div>
    </div>

    <!-- 4 Kartu Statistik Kasir Hari Ini -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Penerimaan Kasir Hari Ini -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Penerimaan Hari Ini</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>Total transaksi lunas</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <!-- Menunggu Pembayaran (Perlu Aksi Kasir) -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition {{ $todayPendingCount > 0 ? 'ring-2 ring-amber-500/30' : '' }}">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Menunggu Bayar</p>
                <h3 class="text-2xl font-black {{ $todayPendingCount > 0 ? 'text-rose-600' : 'text-espresso-950' }} mt-1">
                    {{ $todayPendingCount }} <span class="text-sm font-normal text-espresso-500">Pesanan</span>
                </h3>
                <p class="text-[11px] {{ $todayPendingCount > 0 ? 'text-amber-700 font-bold' : 'text-espresso-500' }} mt-1">
                    {{ $todayPendingCount > 0 ? 'Perlu konfirmasi pembayaran' : 'Semua sudah diselesaikan' }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-rose-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <!-- Pesanan Lunas Hari Ini -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Pesanan Lunas</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">
                    {{ $todayPaidOrders }} <span class="text-sm font-normal text-espresso-500">Pesanan</span>
                </h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    Sedang diproses / selesai
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>

        <!-- Total Pesanan Hari Ini -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Total Masuk</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">
                    {{ $totalOrdersCount }} <span class="text-sm font-normal text-espresso-500">Pesanan</span>
                </h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    Volume order meja hari ini
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

    </div>

    <!-- Filter & Pencarian Cepat -->
    <div class="bg-white rounded-3xl border border-cream-200/90 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.cashier.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
            <!-- Quick Status Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <a href="{{ route('admin.cashier.index', ['status' => 'all_active', 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'all_active' ? 'bg-espresso-950 text-white shadow-xs' : 'bg-cream-100 text-espresso-700 hover:bg-cream-200' }}">
                    <span>Semua Aktif</span>
                </a>

                <a href="{{ route('admin.cashier.index', ['status' => 'pending', 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-cream-100 text-espresso-700 hover:bg-cream-200' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Menunggu Bayar</span>
                    @if($todayPendingCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $statusFilter === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} font-black">
                            {{ $todayPendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.cashier.index', ['status' => 'paid', 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'paid' ? 'bg-espresso-950 text-white shadow-xs' : 'bg-cream-100 text-espresso-700 hover:bg-cream-200' }}">
                    <span>Sudah Dibayar</span>
                </a>

                <a href="{{ route('admin.cashier.index', ['status' => 'processing', 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'processing' ? 'bg-espresso-950 text-white shadow-xs' : 'bg-cream-100 text-espresso-700 hover:bg-cream-200' }}">
                    <span>Sedang Diseduh Barista</span>
                </a>

                <a href="{{ route('admin.cashier.index', ['status' => 'completed', 'search' => $search]) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === 'completed' ? 'bg-espresso-950 text-white shadow-xs' : 'bg-cream-100 text-espresso-700 hover:bg-cream-200' }}">
                    <span>Selesai</span>
                </a>
            </div>

            <!-- Search Field -->
            <div class="relative w-full md:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-espresso-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari No Order, Meja, Nama..." 
                       class="w-full bg-cream-50 border border-cream-300 rounded-xl pl-9 pr-3 py-2 text-xs text-espresso-900 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            </div>
        </form>
    </div>

    <!-- Tabel Pesanan Kasir -->
    <div class="bg-white rounded-3xl border border-cream-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-cream-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-espresso-950">Daftar Antrean Kasir</h3>
                <p class="text-[11px] text-espresso-500 mt-0.5">Pantau pesanan yang masuk dan konfirmasi pembayaran tunai langsung di meja kasir.</p>
            </div>
            <span class="text-xs font-bold text-espresso-600 bg-cream-100 px-3 py-1 rounded-full border border-cream-200">
                {{ $orders->count() }} Pesanan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-espresso-900 border-collapse">
                <thead class="bg-cream-50/80 text-[10px] font-extrabold text-espresso-500 uppercase tracking-wider border-b border-cream-200">
                    <tr>
                        <th class="py-3.5 px-4">Waktu (WITA)</th>
                        <th class="py-3.5 px-4">Order & Meja</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Menu Dipesan</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-cream-50/60 transition {{ $order->status === 'pending_payment' ? 'bg-amber-50/30' : '' }}">
                            <!-- Waktu -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-espresso-950 block">{{ $order->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-espresso-400 block">{{ $order->created_at->diffForHumans(null, true) }} lalu</span>
                            </td>

                            <!-- Order & Meja -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-black text-espresso-950 block">{{ $order->order_number }}</span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-amber-800 bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20 mt-0.5">
                                    <i class="fa-solid fa-chair text-[9px]"></i>
                                    <span>{{ $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0') }}</span>
                                </span>
                            </td>

                            <!-- Pelanggan -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-espresso-900 block truncate max-w-[130px]">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-espresso-500 font-mono">{{ $order->customer_whatsapp }}</span>
                            </td>

                            <!-- Menu Items -->
                            <td class="py-3.5 px-4 max-w-[200px]">
                                <span class="font-medium text-espresso-800 block truncate">
                                    {{ $order->items->map(fn($i) => $i->quantity.'x '.$i->menu?->name)->implode(', ') }}
                                </span>
                                <span class="text-[10px] text-espresso-400">{{ $order->items->sum('quantity') }} item total</span>
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 font-black text-amber-700 whitespace-nowrap text-sm">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($order->status === 'pending_payment')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>Menunggu Bayar</span>
                                    </span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Dibayar</span>
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-spin"></span>
                                        <span>Diproses</span>
                                    </span>
                                @elseif($order->status === 'ready')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="fa-solid fa-bell-concierge text-[9px]"></i>
                                        <span>Siap</span>
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-stone-100 text-stone-700 border border-stone-300">
                                        <i class="fa-solid fa-check-double text-[9px]"></i>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        <span>Batal</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Kasir -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($order->status === 'pending_payment')
                                        <!-- Tombol Terima Bayar Tunai -->
                                        <form action="{{ route('admin.cashier.status', $order) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" 
                                                    onclick="return confirm('Konfirmasi terima pembayaran tunai Rp {{ number_format($order->total_amount, 0, ',', '.') }} untuk {{ $order->order_number }}?')" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition cursor-pointer">
                                                <i class="fa-solid fa-check"></i>
                                                <span>Terima Bayar</span>
                                            </button>
                                        </form>

                                        <!-- Tombol Batalkan -->
                                        <form action="{{ route('admin.cashier.status', $order) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" 
                                                    onclick="return confirm('Yakin ingin membatalkan pesanan {{ $order->order_number }}?')" 
                                                    class="p-1.5 rounded-xl text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Batalkan Pesanan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Tombol Lihat Rincian & Cetak Struk -->
                                        <button type="button" 
                                                @click="viewOrder({{ json_encode([
                                                    'id' => $order->id,
                                                    'order_number' => $order->order_number,
                                                    'table' => $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0'),
                                                    'customer_name' => $order->customer_name,
                                                    'customer_whatsapp' => $order->customer_whatsapp,
                                                    'total_amount' => number_format($order->total_amount, 0, ',', '.'),
                                                    'status' => $order->status,
                                                    'time' => $order->created_at->format('d M Y H:i') . ' WITA',
                                                    'items' => $order->items->map(fn($i) => [
                                                        'name' => $i->menu->name ?? 'Menu',
                                                        'quantity' => $i->quantity,
                                                        'price' => number_format($i->price, 0, ',', '.'),
                                                        'subtotal' => number_format($i->price * $i->quantity, 0, ',', '.'),
                                                        'variant' => $i->variant_name,
                                                        'notes' => $i->notes
                                                    ])
                                                ]) }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-cream-100 hover:bg-cream-200 text-espresso-800 border border-cream-300 transition cursor-pointer">
                                            <i class="fa-solid fa-receipt text-amber-600"></i>
                                            <span>Rincian</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-espresso-400">
                                <i class="fa-solid fa-cash-register text-3xl mb-2 text-espresso-300"></i>
                                <p class="text-xs font-bold text-espresso-700">Belum ada pesanan aktif</p>
                                <p class="text-[11px] text-espresso-500 mt-0.5">Pesanan baru yang dibuat pelanggan akan otomatis muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Rincian & Cetak Struk Kasir -->
    <div x-show="selectedOrder" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-espresso-950/60 backdrop-blur-xs transition-opacity" 
                 aria-hidden="true" 
                 @click="selectedOrder = null"></div>

            <div class="relative z-10 inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-cream-200"
                 x-show="selectedOrder">
                 
                <!-- Receipt Header -->
                <div class="bg-espresso-950 px-6 py-4 text-cream-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-600 flex items-center justify-center text-white text-xs">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase text-white">Brader Cafe</h3>
                            <p class="text-[9px] text-cream-300">Struk Pembayaran Kasir</p>
                        </div>
                    </div>
                    <button @click="selectedOrder = null" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 text-cream-200 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4" id="printable-receipt">
                    <!-- Order Info -->
                    <div class="flex justify-between items-start text-xs border-b border-cream-200 pb-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-espresso-400 block">No. Order</span>
                            <span class="font-mono font-black text-espresso-950" x-text="selectedOrder?.order_number"></span>
                            <span class="text-[10px] text-espresso-500 block mt-0.5" x-text="selectedOrder?.time"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-espresso-400 block">Meja & Pemesan</span>
                            <span class="font-bold text-espresso-900 block" x-text="selectedOrder?.table"></span>
                            <span class="text-[11px] text-espresso-600 font-medium" x-text="selectedOrder?.customer_name"></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2 py-2">
                        <template x-for="(item, idx) in selectedOrder?.items" :key="idx">
                            <div class="flex justify-between items-start text-xs border-b border-cream-100 pb-2">
                                <div class="pr-2">
                                    <div class="font-extrabold text-espresso-950">
                                        <span x-text="item.quantity + 'x'"></span>
                                        <span x-text="item.name"></span>
                                    </div>
                                    <template x-if="item.variant">
                                        <span class="text-[10px] text-amber-700 bg-amber-500/10 px-1.5 py-0.2 rounded font-semibold inline-block mt-0.5" x-text="item.variant"></span>
                                    </template>
                                    <template x-if="item.notes">
                                        <p class="text-[10px] text-espresso-500 italic mt-0.5" x-text="'Catatan: ' + item.notes"></p>
                                    </template>
                                </div>
                                <span class="font-bold text-espresso-900 whitespace-nowrap" x-text="'Rp ' + item.subtotal"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Total -->
                    <div class="pt-2 border-t border-cream-200 flex justify-between items-center text-sm">
                        <span class="font-bold text-espresso-800">Total Pembayaran</span>
                        <span class="font-black text-amber-700 text-lg" x-text="'Rp ' + selectedOrder?.total_amount"></span>
                    </div>
                </div>

                <!-- Modal Action Footer -->
                <div class="p-4 bg-cream-50 border-t border-cream-200 flex items-center justify-between gap-3">
                    <button type="button" 
                            @click="selectedOrder = null" 
                            class="px-4 py-2.5 rounded-xl border border-cream-300 text-espresso-700 hover:bg-cream-100 font-bold text-xs">
                        Tutup
                    </button>

                    <button type="button" 
                            @click="printReceipt()" 
                            class="flex-1 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-extrabold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-amber-600/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Struk Kertas</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function cashierStation() {
        return {
            selectedOrder: null,
            viewOrder(orderData) {
                this.selectedOrder = orderData;
            },
            printReceipt() {
                window.print();
            }
        }
    }
</script>
@endpush
