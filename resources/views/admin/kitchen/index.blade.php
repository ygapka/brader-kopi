@extends('layouts.admin')

@section('title', 'KDS Barista (Kitchen Display System)')

@section('content')
<div x-data="kdsBoard()" x-init="initKds()" class="space-y-6">
    
    <!-- Top Control Bar -->
    <div class="bg-white rounded-3xl p-5 border border-cream-200 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-2xl font-bold shadow-xs">
                <i class="fa-solid fa-fire-burner"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-espresso-950 tracking-tight">KDS Barista & Dapur</h2>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        LIVE
                    </span>
                </div>
                <p class="text-xs text-espresso-600 mt-0.5">Kanban board manajemen antrean minuman & makanan secara real-time.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Audio Sound Toggle -->
            <button type="button" 
                    @click="soundEnabled = !soundEnabled; if(soundEnabled) playChime();"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-2 cursor-pointer"
                    :class="soundEnabled 
                        ? 'bg-amber-500/10 border-amber-500/40 text-amber-800' 
                        : 'bg-cream-100 border-cream-300 text-espresso-600 hover:bg-cream-200'">
                <i class="fa-solid" :class="soundEnabled ? 'fa-volume-high text-amber-600' : 'fa-volume-xmark text-espresso-400'"></i>
                <span x-text="soundEnabled ? 'Notifikasi Suara: Nyala' : 'Suara: Mati'"></span>
            </button>

            <!-- Auto Refresh Indicator -->
            <div class="flex items-center gap-2 bg-cream-100 border border-cream-200/90 px-3.5 py-2 rounded-xl text-xs font-bold text-espresso-700">
                <i class="fa-solid fa-clock-rotate-left text-espresso-400"></i>
                <span>Refresh: <span class="text-amber-700 font-mono" x-text="timer + 's'"></span></span>
            </div>

            <!-- Manual Refresh -->
            <button type="button" 
                    onclick="window.location.reload()" 
                    class="px-4 py-2 rounded-xl bg-espresso-950 text-white hover:bg-espresso-900 active:scale-95 text-xs font-extrabold shadow-sm flex items-center gap-2 cursor-pointer transition">
                <i class="fa-solid fa-rotate-right"></i>
                <span>Segarkan</span>
            </button>
        </div>
    </div>

    <!-- Kanban Board 3 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- KOLOM 1: Pesanan Masuk (Paid) -->
        <div class="bg-amber-500/5 rounded-3xl border border-amber-500/20 flex flex-col overflow-hidden shadow-xs"
             @dragover.prevent
             @drop.prevent="handleDrop('paid')">
            
            <!-- Column Header -->
            <div class="p-4 bg-white/80 backdrop-blur-md border-b border-amber-500/20 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                    <div>
                        <h3 class="font-extrabold text-espresso-950 text-sm">Pesanan Masuk</h3>
                        <p class="text-[10px] text-espresso-500">Menunggu barista mulai seduh</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-xl bg-amber-600 text-white text-xs font-black flex items-center justify-center shadow-xs">
                    {{ $orders->where('status', 'paid')->count() }}
                </span>
            </div>

            <!-- Column Cards Area -->
            <div class="p-4 space-y-4 min-h-[500px] overflow-y-auto max-h-[75vh]">
                @forelse($orders->where('status', 'paid') as $order)
                    <div id="order-card-{{ $order->id }}" 
                         draggable="true"
                         @dragstart="handleDragStart({{ $order->id }})"
                         class="bg-white rounded-2xl p-4.5 border border-cream-200/90 shadow-sm hover:shadow-md transition-all border-l-4 border-l-amber-500 relative cursor-grab active:cursor-grabbing">
                        
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-cream-100">
                            <div>
                                <div class="font-mono font-black text-sm text-espresso-950 flex items-center gap-1.5">
                                    <span>{{ $order->order_number }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 bg-amber-500/10 text-amber-800 text-[11px] font-extrabold px-2 py-0.5 rounded-md border border-amber-500/20">
                                        <i class="fa-solid fa-chair text-[10px]"></i>
                                        <span>{{ $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0') }}</span>
                                    </span>
                                    <span class="text-[11px] text-espresso-600 font-medium truncate max-w-[100px]">{{ $order->customer_name }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-espresso-400 block font-mono">{{ $order->created_at->format('H:i') }}</span>
                                <span class="text-[9px] text-amber-700 font-semibold">{{ $order->created_at->diffForHumans(null, true) }} lalu</span>
                            </div>
                        </div>

                        <!-- Card Items List -->
                        <div class="py-3 space-y-2 border-b border-cream-100">
                            @foreach($order->items as $item)
                                <div class="text-xs text-espresso-900">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-espresso-950">{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }}</span>
                                    </div>
                                    @if($item->variant_name)
                                        <span class="inline-block text-[10px] font-semibold text-amber-800 bg-amber-500/10 px-1.5 py-0.2 rounded mt-0.5">
                                            {{ $item->variant_name }}
                                        </span>
                                    @endif
                                    @if($item->notes)
                                        <p class="text-[10px] text-rose-700 bg-rose-50 px-2 py-1 rounded-md font-semibold mt-1 border border-rose-200/60">
                                            <i class="fa-solid fa-comment-dots mr-1"></i>{{ $item->notes }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Card Action -->
                        <div class="pt-3">
                            <form action="{{ route('admin.kitchen.status', $order) }}" method="POST" @submit.prevent="updateStatusAjax({{ $order->id }}, 'processing', $el)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" 
                                        class="w-full bg-amber-600 hover:bg-amber-700 active:scale-95 text-white py-2.5 px-4 rounded-xl text-xs font-black shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-mug-hot"></i>
                                    <span>Mulai Proses Seduh</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-espresso-400">
                        <i class="fa-solid fa-check-circle text-3xl mb-2 text-espresso-300"></i>
                        <p class="text-xs font-bold text-espresso-700">Tidak ada pesanan baru</p>
                        <p class="text-[11px] text-espresso-500 mt-0.5">Semua pesanan baru sudah ditangani.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KOLOM 2: Sedang Diproses (Processing) -->
        <div class="bg-blue-500/5 rounded-3xl border border-blue-500/20 flex flex-col overflow-hidden shadow-xs"
             @dragover.prevent
             @drop.prevent="handleDrop('processing')">
            
            <!-- Column Header -->
            <div class="p-4 bg-white/80 backdrop-blur-md border-b border-blue-500/20 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-blue-500 animate-spin"></span>
                    <div>
                        <h3 class="font-extrabold text-espresso-950 text-sm">Sedang Diproses</h3>
                        <p class="text-[10px] text-espresso-500">Barista sedang menyiapkan minuman</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-xl bg-blue-600 text-white text-xs font-black flex items-center justify-center shadow-xs">
                    {{ $orders->where('status', 'processing')->count() }}
                </span>
            </div>

            <!-- Column Cards Area -->
            <div class="p-4 space-y-4 min-h-[500px] overflow-y-auto max-h-[75vh]">
                @forelse($orders->where('status', 'processing') as $order)
                    <div id="order-card-{{ $order->id }}" 
                         draggable="true"
                         @dragstart="handleDragStart({{ $order->id }})"
                         class="bg-white rounded-2xl p-4.5 border border-cream-200/90 shadow-sm hover:shadow-md transition-all border-l-4 border-l-blue-500 relative cursor-grab active:cursor-grabbing">
                        
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-cream-100">
                            <div>
                                <div class="font-mono font-black text-sm text-espresso-950 flex items-center gap-1.5">
                                    <span>{{ $order->order_number }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 bg-blue-500/10 text-blue-800 text-[11px] font-extrabold px-2 py-0.5 rounded-md border border-blue-500/20">
                                        <i class="fa-solid fa-chair text-[10px]"></i>
                                        <span>{{ $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0') }}</span>
                                    </span>
                                    <span class="text-[11px] text-espresso-600 font-medium truncate max-w-[100px]">{{ $order->customer_name }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-espresso-400 block font-mono">{{ $order->created_at->format('H:i') }}</span>
                                <span class="text-[9px] text-blue-700 font-bold bg-blue-50 px-1.5 py-0.5 rounded">Sedang Diseduh</span>
                            </div>
                        </div>

                        <!-- Card Items List with interactive checklist -->
                        <div class="py-3 space-y-2 border-b border-cream-100" x-data="{ checked: {} }">
                            <span class="text-[9px] text-espresso-400 uppercase font-bold tracking-wider block mb-1">Daftar Minuman (Ketuk jika siap)</span>
                            @foreach($order->items as $index => $item)
                                <div @click="checked[{{ $index }}] = !checked[{{ $index }}]" 
                                     class="text-xs text-espresso-900 cursor-pointer p-1.5 rounded-lg transition"
                                     :class="checked[{{ $index }}] ? 'bg-emerald-50 text-emerald-800 line-through opacity-60' : 'hover:bg-cream-100'">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold">{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }}</span>
                                        <i class="fa-regular" :class="checked[{{ $index }}] ? 'fa-square-check text-emerald-600' : 'fa-square text-espresso-300'"></i>
                                    </div>
                                    @if($item->variant_name)
                                        <span class="inline-block text-[10px] font-semibold text-espresso-600">
                                            Varian: {{ $item->variant_name }}
                                        </span>
                                    @endif
                                    @if($item->notes)
                                        <p class="text-[10px] text-rose-700 font-semibold italic mt-0.5">Catatan: {{ $item->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Card Action -->
                        <div class="pt-3">
                            <form action="{{ route('admin.kitchen.status', $order) }}" method="POST" @submit.prevent="updateStatusAjax({{ $order->id }}, 'ready', $el)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white py-2.5 px-4 rounded-xl text-xs font-black shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-bell-concierge"></i>
                                    <span>Siap Disajikan</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-espresso-400">
                        <i class="fa-solid fa-mug-saucer text-3xl mb-2 text-espresso-300"></i>
                        <p class="text-xs font-bold text-espresso-700">Tidak ada pesanan diproses</p>
                        <p class="text-[11px] text-espresso-500 mt-0.5">Mulai proses dari kolom Pesanan Masuk.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KOLOM 3: Selesai / Siap Diantar (Ready & Completed) -->
        <div class="bg-emerald-500/5 rounded-3xl border border-emerald-500/20 flex flex-col overflow-hidden shadow-xs"
             @dragover.prevent
             @drop.prevent="handleDrop('completed')">
            
            <!-- Column Header -->
            <div class="p-4 bg-white/80 backdrop-blur-md border-b border-emerald-500/20 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <div>
                        <h3 class="font-extrabold text-espresso-950 text-sm">Siap & Selesai</h3>
                        <p class="text-[10px] text-espresso-500">Pesanan siap diantar / sudah selesai</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white text-xs font-black flex items-center justify-center shadow-xs">
                    {{ $orders->whereIn('status', ['ready', 'completed'])->count() }}
                </span>
            </div>

            <!-- Column Cards Area -->
            <div class="p-4 space-y-4 min-h-[500px] overflow-y-auto max-h-[75vh]">
                @forelse($orders->whereIn('status', ['ready', 'completed']) as $order)
                    <div id="order-card-{{ $order->id }}" 
                         class="bg-white rounded-2xl p-4.5 border border-cream-200/90 shadow-sm transition-all border-l-4 {{ $order->status === 'ready' ? 'border-l-emerald-500' : 'border-l-espresso-400 opacity-80' }}">
                        
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-2 pb-3 border-b border-cream-100">
                            <div>
                                <div class="font-mono font-black text-sm text-espresso-950 flex items-center gap-1.5">
                                    <span>{{ $order->order_number }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-800 text-[11px] font-extrabold px-2 py-0.5 rounded-md border border-emerald-500/20">
                                        <i class="fa-solid fa-chair text-[10px]"></i>
                                        <span>{{ $order->table->display_name ?? 'Meja ' . ($order->table->table_number ?? '0') }}</span>
                                    </span>
                                    <span class="text-[11px] text-espresso-600 font-medium truncate max-w-[100px]">{{ $order->customer_name }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($order->status === 'ready')
                                    <span class="text-[9px] text-emerald-800 font-black bg-emerald-100 px-2 py-0.5 rounded-full inline-block animate-pulse">
                                        Siap Diantar
                                    </span>
                                @else
                                    <span class="text-[9px] text-espresso-600 font-bold bg-cream-200 px-2 py-0.5 rounded-full inline-block">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Items List -->
                        <div class="py-3 space-y-1.5 border-b border-cream-100">
                            @foreach($order->items as $item)
                                <div class="text-xs font-bold text-espresso-900 flex justify-between">
                                    <span>{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }}</span>
                                    @if($item->variant_name)
                                        <span class="text-[10px] text-espresso-500 font-medium">({{ $item->variant_name }})</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Card Action -->
                        <div class="pt-3">
                            @if($order->status === 'ready')
                                <form action="{{ route('admin.kitchen.status', $order) }}" method="POST" @submit.prevent="updateStatusAjax({{ $order->id }}, 'completed', $el)">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" 
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white py-2.5 px-4 rounded-xl text-xs font-black shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Tandai Diantar (Selesai)</span>
                                    </button>
                                </form>
                            @else
                                <div class="text-center py-1 text-[11px] font-bold text-emerald-700 flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Pesanan Sudah Diserahkan</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-espresso-400">
                        <i class="fa-solid fa-clipboard-check text-3xl mb-2 text-espresso-300"></i>
                        <p class="text-xs font-bold text-espresso-700">Belum ada pesanan selesai</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function kdsBoard() {
        return {
            draggedOrderId: null,
            timer: 15,
            soundEnabled: false,
            
            initKds() {
                // Auto countdown refresh every second
                setInterval(() => {
                    if (this.timer > 1) {
                        this.timer--;
                    } else {
                        this.timer = 15;
                        window.location.reload();
                    }
                }, 1000);
            },
            
            playChime() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                    osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5
                    gain.gain.setValueAtTime(0.1, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.4);
                } catch (e) {}
            },
            
            handleDragStart(orderId) {
                this.draggedOrderId = orderId;
            },
            
            handleDrop(targetStatus) {
                if (!this.draggedOrderId) return;
                const orderId = this.draggedOrderId;
                this.draggedOrderId = null;
                
                // Kirim request update status
                this.updateStatus(orderId, targetStatus);
            },
            
            async updateStatus(orderId, status) {
                try {
                    const res = await fetch(`/admin/kitchen/${orderId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status })
                    });
                    
                    if (res.ok) {
                        window.location.reload();
                    } else {
                        alert('Gagal memperbarui status.');
                    }
                } catch (e) {
                    window.location.reload();
                }
            },
            
            async updateStatusAjax(orderId, status, formElement) {
                const btn = formElement.querySelector('button');
                if (btn) btn.disabled = true;
                await this.updateStatus(orderId, status);
            }
        }
    }
</script>
@endpush
