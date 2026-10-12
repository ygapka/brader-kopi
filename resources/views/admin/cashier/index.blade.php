@extends('layouts.admin')

@section('title', 'Kasir - Order Monitor')

@section('content')
<div class="px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">Monitor Pesanan (Kasir)</h2>
            <p class="text-stone-500 text-sm mt-1">Pantau semua pesanan masuk secara real-time.</p>
        </div>
        <button onclick="window.location.reload()" class="bg-white border border-stone-200 text-stone-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-stone-50 shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-rotate-right"></i> Refresh
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-600">
                <thead class="bg-stone-50/80 text-stone-500 font-semibold uppercase text-xs tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Nomor Order</th>
                        <th class="px-6 py-4">Meja</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-stone-800">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">Meja {{ $order->table->table_number }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-stone-800">{{ $order->customer_name }}</div>
                                <div class="text-xs text-stone-500">{{ $order->customer_whatsapp }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-amber-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending_payment' => 'bg-rose-100 text-rose-700',
                                        'paid' => 'bg-emerald-100 text-emerald-700',
                                        'processing' => 'bg-blue-100 text-blue-700',
                                        'ready' => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-stone-100 text-stone-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    $statusLabels = [
                                        'pending_payment' => 'Menunggu Pembayaran',
                                        'paid' => 'Dibayar',
                                        'processing' => 'Diproses',
                                        'ready' => 'Siap Diantar',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($order->status === 'pending_payment')
                                    <form action="{{ route('admin.cashier.status', $order) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" onclick="return confirm('Tandai pesanan ini lunas?')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                                            <i class="fa-solid fa-check mr-1"></i> Tandai Lunas
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="alert('Detail item: \n{{ $order->items->map(fn($i) => $i->quantity . 'x ' . $i->menu->name . ' (' . ($i->variant_name ?? '-') . ')')->join('\n') }}')" class="bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                                        Lihat Item
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-stone-500">
                                <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center mx-auto mb-3 text-stone-400 text-2xl">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <p>Belum ada pesanan masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

