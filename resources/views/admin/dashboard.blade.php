@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')
<div class="space-y-6">
    
    <!-- Top Greeting & Quick Action Bar -->
    <div class="bg-gradient-to-r from-espresso-950 via-espresso-900 to-espresso-850 rounded-3xl p-6 text-cream-50 shadow-md shadow-espresso-950/15 flex flex-col md:flex-row md:items-center md:justify-between gap-5 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-2 text-amber-400 text-xs font-bold uppercase tracking-wider mb-1">
                <i class="fa-solid fa-mug-hot"></i>
                <span>Brader Cafe Control Center</span>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Ringkasan Bisnis & Operasional</h2>
            <p class="text-xs text-cream-300 mt-1 max-w-lg">
                Pantau omzet penjualan, kinerja barista di dapur, dan riwayat pesanan meja secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 flex-wrap">
            <a href="{{ route('admin.kitchen.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-black shadow-md shadow-amber-600/30 transition">
                <i class="fa-solid fa-fire-burner"></i>
                <span>Buka KDS Barista</span>
            </a>
            <a href="{{ route('admin.cashier.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-cream-100 text-xs font-bold border border-white/10 transition">
                <i class="fa-solid fa-cash-register text-amber-400"></i>
                <span>Monitor Kasir</span>
            </a>
            <a href="{{ route('admin.menus.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-cream-100 text-xs font-bold border border-white/10 transition">
                <i class="fa-solid fa-plus text-amber-400"></i>
                <span>Menu Baru</span>
            </a>
        </div>

        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- 4 Kartu Statistik Pendapatan & Operasional -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Omzet -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
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

        <!-- Omzet Hari Ini -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Omzet Hari Ini</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-espresso-600 font-semibold mt-1">
                    <span class="font-black text-espresso-900">{{ $todayOrders }}</span> pesanan masuk hari ini
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <!-- Total Pesanan & Rata-rata Nilai -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">{{ $totalOrders }}</h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    AOV: <span class="font-bold text-amber-800">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-espresso-900/10 text-espresso-800 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Okupansi Meja & Menu -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Kapasitas & Meja</p>
                <h3 class="text-2xl font-black text-espresso-950 mt-1">{{ $activeTables }} <span class="text-sm font-medium text-espresso-400">/ {{ $totalTables }}</span></h3>
                <p class="text-[11px] text-espresso-600 font-medium mt-1">
                    <span class="font-bold text-emerald-700">{{ $availableMenus }} menu</span> siap dipesan
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chair"></i>
            </div>
        </div>

    </div>

    <!-- Grafik Penjualan Sederhana (7 Hari Terakhir) -->
    <div class="bg-white rounded-3xl border border-cream-200/90 p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-5 border-b border-cream-100">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-espresso-950">Tren Penjualan 7 Hari Terakhir</h3>
                </div>
                <p class="text-xs text-espresso-500 mt-0.5">Grafik pergerakan nominal omzet kafe dan volume pesanan harian.</p>
            </div>
            
            <div class="flex items-center gap-4 text-xs font-bold text-espresso-700">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-amber-600 inline-block"></span>
                    <span>Omzet Harian (Rp)</span>
                </div>
            </div>
        </div>

        <!-- Bar Chart Visualizer -->
        <div class="pt-6">
            <div class="h-56 flex items-end justify-between gap-3 sm:gap-6 px-2 sm:px-4 border-b border-cream-200">
                @foreach($salesChartData as $bar)
                    @php
                        $percent = $maxDailyRevenue > 0 ? max(8, round(($bar['revenue'] / $maxDailyRevenue) * 100)) : 8;
                    @endphp
                    <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer">
                        
                        <!-- Tooltip Hover Popover -->
                        <div class="absolute -top-14 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20 bg-espresso-950 text-cream-50 text-[10px] font-bold px-3 py-1.5 rounded-xl shadow-xl whitespace-nowrap text-center">
                            <span class="text-amber-400 block">{{ $bar['label'] }}</span>
                            <span>Rp {{ number_format($bar['revenue'], 0, ',', '.') }} ({{ $bar['count'] }} order)</span>
                        </div>

                        <!-- Bar Pillar -->
                        <div class="w-full max-w-[48px] bg-gradient-to-t from-amber-700 to-amber-500 group-hover:from-amber-600 group-hover:to-amber-400 rounded-t-xl transition-all duration-300 relative shadow-xs"
                             style="height: {{ $percent }}%;">
                            @if($bar['count'] > 0)
                                <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-[9px] font-extrabold text-espresso-700">
                                    {{ $bar['count'] }}
                                </span>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- X-Axis Labels -->
            <div class="flex justify-between gap-3 sm:gap-6 px-2 sm:px-4 mt-2">
                @foreach($salesChartData as $bar)
                    <div class="flex-1 text-center">
                        <span class="text-[11px] font-extrabold text-espresso-700 block">{{ $bar['short'] }}</span>
                        <span class="text-[9px] text-espresso-400 block">{{ \Carbon\Carbon::parse($bar['date'])->format('d/m') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pesanan & Audit Log -->
    <div class="bg-white rounded-3xl border border-cream-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-cream-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-espresso-900 text-cream-50 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-espresso-950">Riwayat Pesanan & Audit Log</h3>
                </div>
                <p class="text-xs text-espresso-500 mt-0.5">Catatan riwayat order pelanggan beserta jejak status perubahan (audit trail).</p>
            </div>
            <a href="{{ route('admin.cashier.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1.5 self-start sm:self-auto">
                <span>Buka Monitor Kasir</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-cream-50/80 border-b border-cream-200 text-[10px] font-extrabold text-espresso-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Order & Meja</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Menu Dipesan</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status Pesanan</th>
                        <th class="py-3 px-4">Jejak Status (Audit Log)</th>
                        <th class="py-3 px-4 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-xs">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-cream-50/60 transition">
                            <!-- Order Number & Table -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-black text-espresso-950 block">{{ $order->order_number }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-500/10 px-2 py-0.2 rounded mt-0.5">
                                    <i class="fa-solid fa-chair text-[9px]"></i>
                                    <span>{{ $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0') }}</span>
                                </span>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-espresso-900 block truncate max-w-[130px]">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-espresso-500 font-mono">{{ $order->customer_whatsapp }}</span>
                            </td>

                            <!-- Menu Items Summary -->
                            <td class="py-3.5 px-4 max-w-[200px]">
                                <span class="font-medium text-espresso-800 block truncate">
                                    {{ $order->items->map(fn($i) => $i->quantity.'x '.$i->menu?->name)->implode(', ') }}
                                </span>
                                <span class="text-[10px] text-espresso-400">{{ $order->items->sum('quantity') }} item total</span>
                            </td>

                            <!-- Total Amount -->
                            <td class="py-3.5 px-4 font-black text-espresso-950 whitespace-nowrap">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($order->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Dibayar</span>
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-spin"></span>
                                        <span>Diproses</span>
                                    </span>
                                @elseif($order->status === 'ready')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="fa-solid fa-bell-concierge text-[9px]"></i>
                                        <span>Siap Diantar</span>
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-stone-100 text-stone-700 border border-stone-300">
                                        <i class="fa-solid fa-check-double text-[9px]"></i>
                                        <span>Selesai</span>
                                    </span>
                                @elseif($order->status === 'pending_payment')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Menunggu Bayar</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        <span>Batal</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Audit Log -->
                            <td class="py-3.5 px-4 text-[11px] text-espresso-600 max-w-[200px]">
                                @if($order->statusLogs->isNotEmpty())
                                    @php $lastLog = $order->statusLogs->last(); @endphp
                                    <div class="flex items-center gap-1 font-semibold text-espresso-900">
                                        <i class="fa-solid fa-user-clock text-amber-600 text-[10px]"></i>
                                        <span>{{ ucfirst($lastLog->status) }}</span>
                                        @if($lastLog->updater)
                                            <span class="text-espresso-500 font-normal">oleh {{ $lastLog->updater->name }}</span>
                                        @endif
                                    </div>
                                    <span class="text-[9px] text-espresso-400 block">{{ $lastLog->created_at->format('H:i') }} ({{ $lastLog->created_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-espresso-400 italic text-[10px]">Belum ada log</span>
                                @endif
                            </td>

                            <!-- Time -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <span class="font-mono text-espresso-900 font-bold block">{{ $order->created_at->format('H:i') }}</span>
                                <span class="text-[10px] text-espresso-400 block">{{ $order->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-espresso-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 text-espresso-300"></i>
                                <p class="text-xs font-bold text-espresso-700">Belum ada transaksi pesanan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dua Kolom: Meja Kafe & Menu Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Meja & Status QR -->
        <div class="bg-white rounded-3xl border border-cream-200/90 shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-cream-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="font-extrabold text-espresso-950 text-sm">Daftar Meja Kafe</h3>
                </div>
                <a href="{{ route('admin.tables.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800">
                    Kelola Semua Meja &rarr;
                </a>
            </div>
            <div class="divide-y divide-cream-100 flex-1">
                @forelse($recentTables as $table)
                    <div class="p-3.5 px-5 flex items-center justify-between hover:bg-cream-50/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-cream-200 text-espresso-900 flex items-center justify-center font-black text-xs border border-cream-300">
                                {{ $table->table_number }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-espresso-950">{{ $table->display_name }}</p>
                                <p class="text-[10px] text-espresso-400 font-mono">Token: {{ substr($table->token, 0, 12) }}...</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($table->is_active)
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-stone-100 text-stone-500 border border-stone-200">
                                    Nonaktif
                                </span>
                            @endif
                            <a href="{{ route('admin.tables.print', $table) }}" target="_blank"
                               class="p-2 rounded-xl text-espresso-400 hover:text-amber-700 hover:bg-amber-50 transition" title="Cetak QR Code">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-espresso-400 text-xs">
                        Belum ada meja.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Menu Terbaru -->
        <div class="bg-white rounded-3xl border border-cream-200/90 shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-cream-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 class="font-extrabold text-espresso-950 text-sm">Katalog Menu Terbaru</h3>
                </div>
                <a href="{{ route('admin.menus.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800">
                    Kelola Semua Menu &rarr;
                </a>
            </div>
            <div class="divide-y divide-cream-100 flex-1">
                @forelse($recentMenus as $menu)
                    <div class="p-3.5 px-5 flex items-center justify-between hover:bg-cream-50/60 transition">
                        <div class="flex items-center gap-3">
                            @if($menu->image_url)
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-10 h-10 rounded-xl object-cover border border-cream-200">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center border border-amber-500/20 text-xs">
                                    <i class="fa-solid fa-mug-hot"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-xs font-bold text-espresso-950 leading-tight">{{ $menu->name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-amber-700 font-semibold">{{ $menu->category->name ?? 'Uncategorized' }}</span>
                                    <span class="text-cream-300">&bull;</span>
                                    <span class="text-[10px] font-black text-espresso-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="px-3 py-1 rounded-xl text-[11px] font-extrabold bg-cream-100 hover:bg-amber-50 hover:text-amber-700 text-espresso-700 transition">
                                Edit
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-espresso-400 text-xs">
                        Belum ada menu.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
