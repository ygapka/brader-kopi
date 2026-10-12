@extends('layouts.admin')

@section('title', 'Kitchen Display System')

@section('content')
<div class="px-8 py-8 h-full flex flex-col">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-stone-800">Kitchen Display System</h2>
            <p class="text-stone-500 text-sm mt-1">Daftar pesanan untuk disiapkan dapur/barista.</p>
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

    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 overflow-hidden">
        
        <!-- Kolom: Menunggu Diproses (Paid) -->
        <div class="bg-stone-100 rounded-2xl border border-stone-200 flex flex-col h-full overflow-hidden">
            <div class="p-4 border-b border-stone-200 bg-white/50 backdrop-blur-sm flex justify-between items-center">
                <h3 class="font-bold text-stone-700">Pesanan Baru</h3>
                <span class="bg-stone-200 text-stone-700 text-xs font-bold px-2 py-1 rounded-lg">{{ $orders->where('status', 'paid')->count() }}</span>
            </div>
            <div class="p-4 overflow-y-auto flex-1 space-y-4">
                @foreach($orders->where('status', 'paid') as $order)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-stone-200 border-l-4 border-l-emerald-500">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-stone-800">{{ $order->order_number }}</div>
                                <div class="text-xs text-stone-500">{{ $order->created_at->format('H:i') }} - Meja {{ $order->table->table_number }}</div>
                            </div>
                        </div>
                        <ul class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <li class="text-sm text-stone-700 border-b border-stone-100 pb-2 last:border-0 last:pb-0">
                                    <div class="font-semibold">{{ $item->quantity }}x {{ $item->menu->name }}</div>
                                    @if($item->variant_name)
                                        <div class="text-xs text-amber-600 font-medium">Varian: {{ $item->variant_name }}</div>
                                    @endif
                                    @if($item->notes)
                                        <div class="text-xs text-stone-500 italic mt-0.5">Catatan: {{ $item->notes }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('admin.kitchen.status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                                Mulai Proses
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom: Sedang Diproses (Processing) -->
        <div class="bg-blue-50 rounded-2xl border border-blue-100 flex flex-col h-full overflow-hidden">
            <div class="p-4 border-b border-blue-100 bg-white/50 backdrop-blur-sm flex justify-between items-center">
                <h3 class="font-bold text-blue-800">Sedang Diproses</h3>
                <span class="bg-blue-200 text-blue-800 text-xs font-bold px-2 py-1 rounded-lg">{{ $orders->where('status', 'processing')->count() }}</span>
            </div>
            <div class="p-4 overflow-y-auto flex-1 space-y-4">
                @foreach($orders->where('status', 'processing') as $order)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-200 border-l-4 border-l-blue-500">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-stone-800">{{ $order->order_number }}</div>
                                <div class="text-xs text-stone-500">{{ $order->created_at->format('H:i') }} - Meja {{ $order->table->table_number }}</div>
                            </div>
                        </div>
                        <ul class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <li class="text-sm text-stone-700 border-b border-stone-100 pb-2 last:border-0 last:pb-0">
                                    <div class="font-semibold">{{ $item->quantity }}x {{ $item->menu->name }}</div>
                                    @if($item->variant_name)
                                        <div class="text-xs text-amber-600 font-medium">Varian: {{ $item->variant_name }}</div>
                                    @endif
                                    @if($item->notes)
                                        <div class="text-xs text-stone-500 italic mt-0.5">Catatan: {{ $item->notes }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('admin.kitchen.status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                                Siap Diantar
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom: Siap Diantar (Ready) -->
        <div class="bg-amber-50 rounded-2xl border border-amber-100 flex flex-col h-full overflow-hidden">
            <div class="p-4 border-b border-amber-100 bg-white/50 backdrop-blur-sm flex justify-between items-center">
                <h3 class="font-bold text-amber-800">Siap Diantar</h3>
                <span class="bg-amber-200 text-amber-800 text-xs font-bold px-2 py-1 rounded-lg">{{ $orders->where('status', 'ready')->count() }}</span>
            </div>
            <div class="p-4 overflow-y-auto flex-1 space-y-4">
                @foreach($orders->where('status', 'ready') as $order)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-amber-200 border-l-4 border-l-amber-500">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-stone-800">{{ $order->order_number }}</div>
                                <div class="text-xs text-stone-500">{{ $order->created_at->format('H:i') }} - Meja {{ $order->table->table_number }}</div>
                            </div>
                        </div>
                        <ul class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <li class="text-sm text-stone-700 border-b border-stone-100 pb-2 last:border-0 last:pb-0">
                                    <div class="font-semibold">{{ $item->quantity }}x {{ $item->menu->name }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('admin.kitchen.status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button class="w-full bg-stone-800 hover:bg-stone-900 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                                Tandai Selesai
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection

