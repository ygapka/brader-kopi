@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <h2 class="text-xl font-extrabold text-espresso-950 tracking-tight">Laporan Penjualan</h2>
            </div>
            <p class="text-xs text-espresso-600 mt-1">
                Ringkasan pendapatan omzet kafe dan riwayat seluruh transaksi pesanan pelanggan.
            </p>
        </div>

        <button type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-cream-300 text-espresso-800 hover:bg-cream-100 active:scale-95 text-xs font-bold shadow-xs transition cursor-pointer self-start sm:self-auto">
            <i class="fa-solid fa-print text-amber-600"></i>
            <span>Cetak / Cetak PDF</span>
        </button>
    </div>

    <!-- Filter Card -->
    <div class="bg-white border border-cream-200/90 rounded-3xl p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <!-- Mulai Tanggal -->
            <div>
                <label class="block text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1.5">
                    Mulai Tanggal
                </label>
                <div class="relative">
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           class="w-full bg-cream-50 border border-cream-300 text-espresso-900 text-xs rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 block p-2.5 font-medium">
                </div>
            </div>

            <!-- Sampai Tanggal -->
            <div>
                <label class="block text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1.5">
                    Sampai Tanggal
                </label>
                <div class="relative">
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           class="w-full bg-cream-50 border border-cream-300 text-espresso-900 text-xs rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 block p-2.5 font-medium">
                </div>
            </div>

            <!-- Status Transaksi Filter -->
            <div>
                <label class="block text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-1.5">
                    Status Pesanan
                </label>
                <select name="status" class="w-full bg-cream-50 border border-cream-300 text-espresso-900 text-xs rounded-xl focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 block p-2.5 font-medium">
                    <option value="all_paid" {{ $status === 'all_paid' ? 'selected' : '' }}>Semua Transaksi Lunas / Berhasil</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Hanya Selesai (Completed)</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Baru Dibayar (Paid)</option>
                    <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Sedang Diproses / Siap</option>
                    <option value="pending_payment" {{ $status === 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-extrabold rounded-xl text-xs py-2.5 px-4 shadow-md shadow-amber-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Total Pendapatan -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>Akumulasi transaksi lunas</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">
                    {{ number_format($totalOrders, 0, ',', '.') }} <span class="text-sm font-normal text-espresso-500">Pesanan</span>
                </h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    Data sesuai filter rentang waktu
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Rata-rata Nilai Pesanan (AOV) -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Rata-rata Pesanan (AOV)</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1">
                    Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    Rata-rata belanja per meja
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-cream-200/90 rounded-3xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-cream-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-espresso-950">Rincian Transaksi Penjualan</h3>
                <p class="text-[11px] text-espresso-500 mt-0.5">Daftar transaksi yang tercatat dalam sistem Brader Cafe.</p>
            </div>
            <span class="text-xs font-bold text-espresso-600 bg-cream-100 px-3 py-1 rounded-full border border-cream-200 self-start sm:self-auto">
                {{ $orders->count() }} Data Ditemukan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-espresso-900 border-collapse">
                <thead class="bg-cream-50/80 text-[10px] font-extrabold text-espresso-500 uppercase tracking-wider border-b border-cream-200">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4">Nomor Order</th>
                        <th class="py-3.5 px-4">Meja</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Rincian Menu</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-cream-50/60 transition">
                            <!-- Tanggal & Waktu -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-espresso-950 block">{{ $order->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-espresso-400 font-mono">{{ $order->created_at->format('H:i') }} WITA</span>
                            </td>

                            <!-- Nomor Order -->
                            <td class="py-3.5 px-4 font-mono font-black text-espresso-950 whitespace-nowrap">
                                {{ $order->order_number }}
                            </td>

                            <!-- Meja -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-amber-800 bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/20">
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
                            <td class="py-3.5 px-4 max-w-[220px]">
                                <span class="font-medium text-espresso-800 block truncate" title="{{ $order->items->map(fn($i) => $i->quantity.'x '.$i->menu?->name)->implode(', ') }}">
                                    {{ $order->items->map(fn($i) => $i->quantity.'x '.$i->menu?->name)->implode(', ') }}
                                </span>
                                <span class="text-[10px] text-espresso-400">{{ $order->items->sum('quantity') }} item total</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($order->status === 'paid')
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
                                @elseif($order->status === 'pending_payment')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Menunggu Bayar</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        <span>Batal</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Total -->
                            <td class="py-3.5 px-4 font-black text-espresso-950 text-right whitespace-nowrap">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-espresso-400">
                                <i class="fa-solid fa-file-circle-xmark text-3xl mb-2 text-espresso-300"></i>
                                <p class="text-xs font-bold text-espresso-700">Tidak ada data transaksi pada rentang filter ini.</p>
                                <p class="text-[11px] text-espresso-500 mt-0.5">Coba ubah tanggal atau status filter di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
