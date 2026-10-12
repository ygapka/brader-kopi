@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Utama</h2>
        <p class="text-sm text-slate-500">Ringkasan status operasional dan inventaris Brader Cafe.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tables.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold shadow-sm transition">
            <i class="fa-solid fa-qrcode text-amber-600"></i>
            <span>Cetak QR Meja</span>
        </a>
        <a href="{{ route('admin.menus.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-600/20 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>
</div>

<!-- Quick Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Total Meja -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Meja</p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalTables }}</h3>
            <p class="text-xs text-emerald-600 font-medium mt-1">
                <i class="fa-solid fa-circle-check"></i> {{ $activeTables }} meja aktif
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-chair"></i>
        </div>
    </div>

    <!-- Total Kategori -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori Menu</p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalCategories }}</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Kelompok produk kafe
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-tags"></i>
        </div>
    </div>

    <!-- Total Menu -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Item Menu</p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalMenus }}</h3>
            <p class="text-xs text-emerald-600 font-medium mt-1">
                <i class="fa-solid fa-circle-check"></i> {{ $availableMenus }} siap dipesan
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-mug-saucer"></i>
        </div>
    </div>

    <!-- Total Transaksi -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pesanan</p>
            <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalOrders }}</h3>
            <p class="text-xs text-amber-600 font-medium mt-1">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }} omzet
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
            <i class="fa-solid fa-receipt"></i>
        </div>
    </div>
</div>

<!-- Two Columns Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Meja & Status QR Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Meja Kafe</h3>
            </div>
            <a href="{{ route('admin.tables.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700">
                Kelola Semua Meja &rarr;
            </a>
        </div>
        <div class="divide-y divide-slate-100 flex-1">
            @forelse($recentTables as $table)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-200">
                            {{ $table->table_number }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $table->table_number }}</p>
                            <p class="text-xs text-slate-400 font-mono">Token: {{ $table->token }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($table->is_active)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                Nonaktif
                            </span>
                        @endif
                        <a href="{{ route('admin.tables.print', $table) }}" target="_blank"
                           class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Cetak QR Code">
                            <i class="fa-solid fa-print"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">
                    Belum ada data meja. Tambahkan meja baru.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Menu Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Menu Unggulan & Terbaru</h3>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700">
                Kelola Semua Menu &rarr;
            </a>
        </div>
        <div class="divide-y divide-slate-100 flex-1">
            @forelse($recentMenus as $menu)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/60 transition">
                    <div class="flex items-center gap-3">
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200">
                        @else
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 font-bold">
                                <i class="fa-solid fa-mug-hot"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-slate-900 leading-tight">{{ $menu->name }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-amber-600 font-medium">{{ $menu->category->name ?? 'Uncategorized' }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs font-bold text-slate-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.menus.edit', $menu) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-amber-50 hover:text-amber-700 text-slate-600 transition">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">
                    Belum ada menu terdaftar.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
