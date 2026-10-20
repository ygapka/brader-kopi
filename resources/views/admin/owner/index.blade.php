@extends('layouts.admin')

@section('title', 'Portal Eksekutif Pemilik (Owner)')

@section('content')
<div class="space-y-6">

    <!-- Top Executive Header & Banner -->
    <div class="bg-gradient-to-r from-espresso-950 via-espresso-900 to-amber-950 rounded-3xl p-6 sm:p-7 text-cream-50 shadow-lg shadow-espresso-950/20 border border-amber-900/30 relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-black uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>Executive Business Intelligence</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Portal Pemilik & Analitik Bisnis</h1>
                <p class="text-xs sm:text-sm text-cream-300 mt-1 max-w-2xl leading-relaxed">
                    Ringkasan performa finansial, tren penjualan, dan efisiensi operasional Brader Cafe secara komprehensif untuk pengambilan keputusan strategis.
                </p>
                <div class="flex items-center gap-3 text-xs text-amber-200/80 mt-3 font-medium">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check text-amber-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </span>
                    <span class="text-white/30">•</span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-amber-400"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('H:i') }} WITA</span>
                    </span>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3 relative z-10 flex-wrap">
                <button onclick="window.print()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-white text-xs font-bold border border-white/15 backdrop-blur-sm transition cursor-pointer">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak Ringkasan</span>
                </button>
                <a href="{{ route('admin.reports.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-700 active:scale-95 text-white text-xs font-black shadow-md shadow-amber-600/30 transition">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Laporan Penjualan Lengkap</span>
                </a>
            </div>
        </div>

        <!-- Decorative background elements -->
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-amber-700/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- 4 Kartu KPI Keuangan Eksekutif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Omzet Akumulatif -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Total Omzet Kumulatif</span>
                    <h3 class="text-2xl font-black text-espresso-950 mt-1">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-cream-100 flex items-center justify-between text-xs">
                <span class="text-espresso-500 font-medium">Transaksi Sukses:</span>
                <span class="font-bold text-espresso-900">{{ number_format($totalPaidOrders) }} Pesanan</span>
            </div>
        </div>

        <!-- Omzet Bulan Ini & Pertumbuhan -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Omzet Bulan Ini</span>
                    <h3 class="text-2xl font-black text-amber-700 mt-1">
                        Rp {{ number_format($thisMonthRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-500/15 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-cream-100 flex items-center justify-between text-xs">
                @if($monthlyGrowth !== null)
                    <span class="flex items-center gap-1 font-bold {{ $monthlyGrowth >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                        <i class="fa-solid {{ $monthlyGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        <span>{{ ($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) }}% vs bln lalu</span>
                    </span>
                @else
                    <span class="text-espresso-500 font-medium">Bulan berjalan</span>
                @endif
                <span class="text-[11px] text-espresso-400 font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- Omzet Hari Ini -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Pendapatan Hari Ini</span>
                    <h3 class="text-2xl font-black text-espresso-950 mt-1">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-600/15 text-amber-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-cream-100 flex items-center justify-between text-xs">
                <span class="text-espresso-500 font-medium">Pesanan Hari Ini:</span>
                <span class="font-extrabold text-amber-800">{{ $todayOrders }} Pesanan</span>
            </div>
        </div>

        <!-- Average Order Value (AOV) -->
        <div class="p-5 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-espresso-400 uppercase tracking-wider">Rata-rata Belanja (AOV)</span>
                    <h3 class="text-2xl font-black text-espresso-950 mt-1">
                        Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-indigo-500/15 text-indigo-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-cream-100 flex items-center justify-between text-xs">
                <span class="text-espresso-500 font-medium">Minggu Ini (7 Hari):</span>
                <span class="font-bold text-espresso-900">Rp {{ number_format($thisWeekRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Grafik Tren Penjualan 14 Hari Terakhir -->
    <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <h2 class="text-base font-extrabold text-espresso-950">Tren Penjualan 14 Hari Terakhir</h2>
                </div>
                <p class="text-xs text-espresso-500 mt-0.5">
                    Grafik akumulasi pendapatan harian yang bersumber dari pesanan lunas.
                </p>
            </div>
            @if($peakTrendDay && $peakTrendDay['revenue'] > 0)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-semibold self-start sm:self-auto">
                <i class="fa-solid fa-trophy text-amber-600"></i>
                <span>Omzet Puncak: <strong>Rp {{ number_format($peakTrendDay['revenue'], 0, ',', '.') }}</strong> ({{ $peakTrendDay['label'] }})</span>
            </div>
            @endif
        </div>

        <!-- Interactive Bar Chart -->
        <div class="relative pt-6 pb-2">
            <!-- Grid Lines Background -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20">
                <div class="border-b border-dashed border-espresso-300 w-full"></div>
                <div class="border-b border-dashed border-espresso-300 w-full"></div>
                <div class="border-b border-dashed border-espresso-300 w-full"></div>
            </div>

            <!-- Bar Columns Grid -->
            <div class="relative grid gap-1.5 sm:gap-2.5 h-48 items-end" style="grid-template-columns: repeat(14, minmax(0, 1fr));">
                @foreach($salesTrend as $day)
                <div class="group relative flex flex-col items-center h-full justify-end">
                    
                    <!-- Hover Tooltip -->
                    <div class="absolute -top-12 z-20 hidden group-hover:flex flex-col items-center pointer-events-none transition-all">
                        <div class="px-2.5 py-1.5 rounded-xl bg-espresso-950 text-white text-[11px] font-bold shadow-xl border border-espresso-800 whitespace-nowrap text-center">
                            <span class="text-amber-400 font-extrabold">Rp {{ number_format($day['revenue'], 0, ',', '.') }}</span>
                            <span class="text-[9px] text-cream-300 block font-medium">{{ $day['count'] }} Transaksi</span>
                        </div>
                        <div class="w-2 h-2 bg-espresso-950 rotate-45 -mt-1"></div>
                    </div>

                    <!-- Bar Stick -->
                    <div class="w-full rounded-t-lg transition-all duration-300 group-hover:brightness-110 cursor-pointer {{ $day['is_today'] ? 'bg-gradient-to-t from-amber-700 to-amber-500 shadow-sm shadow-amber-500/30' : ($day['revenue'] > 0 ? 'bg-gradient-to-t from-espresso-800 to-amber-700' : 'bg-cream-200') }}"
                         style="height: {{ max($day['height_pct'], 4) }}%;">
                    </div>

                    <!-- Day Label Below Bar -->
                    <div class="mt-2 text-center">
                        <span class="block text-[10px] font-extrabold {{ $day['is_today'] ? 'text-amber-700' : 'text-espresso-600' }}">
                            {{ $day['day'] }}
                        </span>
                        <span class="block text-[8px] text-espresso-400 font-medium">
                            {{ $day['label'] }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Metode Pembayaran & Kontribusi Kategori -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Distribusi Metode Pembayaran (QRIS Midtrans vs Tunai) -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-espresso-950">Metode Pembayaran</h2>
                        <p class="text-xs text-espresso-500 mt-0.5">Perbandingan adopsi cashless QRIS vs Tunai di kasir.</p>
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                </div>

                <!-- Stacked Progress Bar -->
                <div class="mt-4">
                    <div class="h-4 w-full bg-cream-200 rounded-full overflow-hidden flex shadow-inner">
                        <div style="width: {{ $qrisPercentage }}%" class="bg-gradient-to-r from-emerald-600 to-teal-500 transition-all duration-500" title="QRIS Midtrans: {{ $qrisPercentage }}%"></div>
                        <div style="width: {{ $cashPercentage }}%" class="bg-gradient-to-r from-amber-600 to-amber-500 transition-all duration-500" title="Tunai Kasir: {{ $cashPercentage }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-bold mt-2">
                        <span class="text-teal-800 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block"></span>
                            <span>QRIS / Online: {{ $qrisPercentage }}%</span>
                        </span>
                        <span class="text-amber-800 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                            <span>Tunai Kasir: {{ $cashPercentage }}%</span>
                        </span>
                    </div>
                </div>

                <!-- Cards Detail 2 Metode -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="p-4 rounded-2xl bg-teal-50/60 border border-teal-200/70">
                        <div class="flex items-center gap-2 text-teal-800 text-xs font-bold mb-1">
                            <i class="fa-solid fa-qrcode text-sm"></i>
                            <span>QRIS & E-Wallet</span>
                        </div>
                        <p class="text-lg font-black text-teal-950">Rp {{ number_format($qrisRevenue, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-teal-700 font-semibold mt-0.5">{{ $qrisCount }} Transaksi Digital</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70">
                        <div class="flex items-center gap-2 text-amber-800 text-xs font-bold mb-1">
                            <i class="fa-solid fa-money-bill-wave text-sm"></i>
                            <span>Tunai (Kasir)</span>
                        </div>
                        <p class="text-lg font-black text-amber-950">Rp {{ number_format($cashRevenue, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-amber-700 font-semibold mt-0.5">{{ $cashCount }} Transaksi Tunai</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-cream-100 text-[11px] text-espresso-500 flex items-center justify-between">
                <span>Total Dana Masuk:</span>
                <span class="font-bold text-espresso-900">Rp {{ number_format($combinedPaymentRevenue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kontribusi Penjualan per Kategori Menu -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-espresso-950">Kontribusi Kategori</h2>
                        <p class="text-xs text-espresso-500 mt-0.5">Persentase kontribusi omzet per kategori menu cafe.</p>
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-espresso-900/10 text-espresso-800 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-tags"></i>
                    </span>
                </div>

                @if($categorySales->count() > 0)
                <div class="space-y-3.5 mt-3">
                    @foreach($categorySales as $category)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-espresso-900">{{ $category->category_name ?? 'Umum' }}</span>
                            <span class="font-extrabold text-amber-800">
                                Rp {{ number_format($category->total_sales, 0, ',', '.') }} 
                                <span class="text-[10px] text-espresso-400 font-semibold">({{ $category->percentage }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-cream-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-600 to-amber-500 h-2.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $category->percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-espresso-500 mt-0.5">
                            <span>Volume terjual: {{ $category->total_qty }} porsi</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-10 text-center text-espresso-400 text-xs">
                    <i class="fa-solid fa-mug-hot text-2xl mb-2 text-espresso-300 block"></i>
                    Belum ada data penjualan kategori.
                </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-cream-100 flex items-center justify-between text-xs">
                <a href="{{ route('admin.menus.index') }}" class="text-amber-700 hover:text-amber-800 font-bold flex items-center gap-1 transition">
                    <span>Lihat Seluruh Katalog Menu</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Top 5 Menu Paling Laris & Analisis Jam Sibuk -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top 5 Menu Paling Laris (Best Sellers) -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-extrabold text-espresso-950">Top 5 Menu Paling Laris</h2>
                    <p class="text-xs text-espresso-500 mt-0.5">Menu terfavorit pelanggan berdasarkan kuantitas pesanan.</p>
                </div>
                <span class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-star"></i>
                </span>
            </div>

            @if($topMenus->count() > 0)
            <div class="space-y-3">
                @foreach($topMenus as $index => $item)
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-cream-50/70 border border-cream-200/70 hover:bg-cream-100/60 transition">
                    <div class="flex items-center gap-3">
                        <!-- Ranking Badge -->
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black shrink-0 {{ $index === 0 ? 'bg-amber-500 text-white shadow-xs shadow-amber-500/40' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700/80 text-white' : 'bg-cream-200 text-espresso-600')) }}">
                            #{{ $index + 1 }}
                        </div>

                        <!-- Menu Image or Icon -->
                        <div class="w-10 h-10 rounded-xl bg-espresso-900/10 flex items-center justify-center overflow-hidden shrink-0 border border-cream-200">
                            @php
                                $imageUrl = $item->image_url ?? null;
                                if (! $imageUrl && ! empty($item->image)) {
                                    $imageUrl = (str_starts_with($item->image, 'http://') || str_starts_with($item->image, 'https://'))
                                        ? $item->image
                                        : asset('storage/'.$item->image);
                                }
                            @endphp
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $item->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none;" class="w-full h-full items-center justify-center bg-cream-200 text-amber-700">
                                    <i class="fa-solid fa-mug-hot"></i>
                                </div>
                            @else
                                <i class="fa-solid fa-mug-hot text-amber-700"></i>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-xs font-extrabold text-espresso-950 leading-tight">{{ $item->name }}</h4>
                            <p class="text-[10px] text-espresso-500 font-semibold">{{ $item->category_name ?? 'Menu' }}</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-xs font-black text-amber-800 block">
                            {{ $item->total_qty }} porsi
                        </span>
                        <span class="text-[10px] text-espresso-500 font-medium">
                            Rp {{ number_format($item->total_sales, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-12 text-center text-espresso-400 text-xs">
                <i class="fa-solid fa-chart-pie text-2xl mb-2 text-espresso-300 block"></i>
                Belum ada data menu yang terjual.
            </div>
            @endif
        </div>

        <!-- Analisis Jam Sibuk Kafe (Peak Operating Hours) -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-extrabold text-espresso-950">Analisis Jam Sibuk Kafe</h2>
                        <p class="text-xs text-espresso-500 mt-0.5">Waktu paling padat pengunjung dalam 30 hari terakhir.</p>
                    </div>
                    <span class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-clock"></i>
                    </span>
                </div>

                <div class="space-y-3.5">
                    <!-- Morning / Lunch -->
                    <div class="p-4 rounded-2xl bg-cream-50/70 border border-cream-200/70">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-sun text-amber-600"></i>
                                <span class="text-xs font-bold text-espresso-950">{{ $timeBlocks['morning']['label'] }}</span>
                            </div>
                            <span class="text-xs font-extrabold text-espresso-900">{{ $timeBlocks['morning']['count'] }} Transaksi</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-espresso-600 font-medium">
                            <span>Omzet Sesi:</span>
                            <span class="font-bold text-amber-800">Rp {{ number_format($timeBlocks['morning']['revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Afternoon -->
                    <div class="p-4 rounded-2xl bg-cream-50/70 border border-cream-200/70">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-cloud-sun text-amber-700"></i>
                                <span class="text-xs font-bold text-espresso-950">{{ $timeBlocks['afternoon']['label'] }}</span>
                            </div>
                            <span class="text-xs font-extrabold text-espresso-900">{{ $timeBlocks['afternoon']['count'] }} Transaksi</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-espresso-600 font-medium">
                            <span>Omzet Sesi:</span>
                            <span class="font-bold text-amber-800">Rp {{ number_format($timeBlocks['afternoon']['revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Evening -->
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/25">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-moon text-amber-700"></i>
                                <span class="text-xs font-extrabold text-amber-950">{{ $timeBlocks['evening']['label'] }}</span>
                            </div>
                            <span class="text-xs font-black text-amber-900">{{ $timeBlocks['evening']['count'] }} Transaksi</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-amber-900 font-medium">
                            <span>Omzet Sesi:</span>
                            <span class="font-bold text-amber-900">Rp {{ number_format($timeBlocks['evening']['revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-espresso-400 mt-4 italic">
                *Data dianalisis berdasarkan jam pemesanan masuk dalam zona waktu WITA.
            </p>
        </div>
    </div>

    <!-- Grid 2 Kolom: Performa Meja & Transaksi Terbesar (VIP Orders) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Performa Meja & Okupansi -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-extrabold text-espresso-950">Okupansi & Meja Favorit</h2>
                    <p class="text-xs text-espresso-500 mt-0.5">Meja dengan perputaran tamu dan transaksi terbanyak.</p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $activeTables }} / {{ $totalTables }} Aktif</span>
                </div>
            </div>

            @if($topTables->count() > 0)
            <div class="space-y-3">
                @foreach($topTables as $table)
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-cream-50/70 border border-cream-200/70">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-espresso-950 text-cream-100 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-espresso-950">{{ $table->table_number }}</h4>
                            <p class="text-[10px] text-espresso-500 font-medium">{{ $table->total_orders }} kali dipesan</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-xs font-black text-amber-800 block">
                            Rp {{ number_format($table->total_spent, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-espresso-400 font-medium">Total Akumulasi</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-espresso-400 text-xs">
                Belum ada transaksi meja yang tercatat.
            </div>
            @endif
        </div>

        <!-- Transaksi Nilai Tertinggi Terbaru (VIP Orders) -->
        <div class="p-6 rounded-3xl bg-white border border-cream-200/90 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-extrabold text-espresso-950">Transaksi Tertinggi (VIP Orders)</h2>
                    <p class="text-xs text-espresso-500 mt-0.5">Pesanan dengan nilai belanja terbesar.</p>
                </div>
                <span class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-gem"></i>
                </span>
            </div>

            @if($recentHighValueOrders->count() > 0)
            <div class="space-y-3">
                @foreach($recentHighValueOrders as $order)
                <div class="p-3.5 rounded-2xl bg-cream-50/70 border border-cream-200/70 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-espresso-900 font-mono">{{ $order->order_number }}</span>
                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 text-[10px] font-bold">
                                {{ $order->table->table_number ?? 'Meja ?' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-espresso-600 mt-0.5">
                            <span class="font-bold">{{ $order->customer_name }}</span> • 
                            <span class="text-espresso-400">{{ $order->items->sum('quantity') }} items</span>
                        </p>
                    </div>

                    <div class="text-right">
                        <span class="text-xs font-black text-amber-800 block">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-espresso-400 font-medium">
                            {{ $order->created_at->translatedFormat('d M, H:i') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center text-espresso-400 text-xs">
                Belum ada transaksi tercatat.
            </div>
            @endif
        </div>
    </div>

</div>

<!-- Styles for Printing -->
<style>
@media print {
    aside, header, button, .no-print {
        display: none !important;
    }
    body {
        background-color: white !important;
        color: black !important;
    }
    .shadow-xs, .shadow-md, .shadow-lg {
        box-shadow: none !important;
    }
}
</style>
@endsection
