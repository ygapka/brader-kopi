@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="px-8 py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-stone-800">Laporan Penjualan</h2>
        <p class="text-stone-500 text-sm mt-1">Ringkasan pendapatan dan riwayat transaksi selesai.</p>
    </div>

    <!-- Filter -->
    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-stone-50 border border-stone-200 text-stone-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-stone-50 border border-stone-200 text-stone-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            </div>
            <button type="submit" class="bg-stone-800 hover:bg-stone-900 text-white font-medium rounded-xl text-sm px-5 py-2.5 transition-colors">
                <i class="fa-solid fa-filter mr-1.5"></i> Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-stone-500">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-stone-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-stone-500">Transaksi Selesai</p>
                <h3 class="text-2xl font-bold text-stone-800">{{ number_format($totalOrders, 0, ',', '.') }} <span class="text-sm font-normal text-stone-500">Pesanan</span></h3>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-stone-200 flex justify-between items-center">
            <h3 class="font-bold text-stone-800">Riwayat Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-600">
                <thead class="bg-stone-50/80 text-stone-500 font-semibold uppercase text-xs tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Nomor Order</th>
                        <th class="px-6 py-4">Meja</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-stone-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">Meja {{ $order->table->table_number }}</td>
                            <td class="px-6 py-4">{{ $order->customer_name }}</td>
                            <td class="px-6 py-4 font-semibold text-stone-800 text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">
                                <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center mx-auto mb-3 text-stone-400 text-2xl">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <p>Tidak ada data pesanan selesai pada rentang tanggal ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

