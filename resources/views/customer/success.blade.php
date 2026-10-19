@extends('layouts.customer')

@section('title', $order->status === 'paid' ? 'Pembayaran Berhasil' : 'Ringkasan & Pembayaran')

@section('content')
<div class="px-4 py-6 flex flex-col items-center">
    
    @if($order->status === 'paid')
        <!-- Tampilan Berhasil / Lunas -->
        <div class="w-16 h-16 bg-emerald-500/15 text-emerald-600 rounded-3xl flex items-center justify-center mb-3 shadow-sm border border-emerald-500/20 animate-bounce">
            <i class="fa-solid fa-circle-check text-3xl"></i>
        </div>
        
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300/50 mb-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Pembayaran Lunas</span>
        </div>
        
        <h1 class="text-2xl font-black text-espresso-950 mb-1 tracking-tight">Pembayaran Berhasil!</h1>
        <p class="text-espresso-600 text-xs max-w-xs text-center mb-6 leading-relaxed">
            Terima kasih, <strong class="text-espresso-900">{{ $order->customer_name }}</strong>! Pembayaran telah kami terima dan barista kami sedang menyiapkan pesanan Anda.
        </p>
    @else
        <!-- Tampilan Menunggu Pembayaran -->
        <div class="w-16 h-16 bg-amber-500/15 text-amber-600 rounded-3xl flex items-center justify-center mb-3 shadow-sm border border-amber-500/20">
            <i class="fa-solid fa-receipt text-3xl"></i>
        </div>

        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300/50 mb-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <span>Menunggu Pembayaran</span>
        </div>

        <h1 class="text-2xl font-black text-espresso-950 mb-1 tracking-tight">Ringkasan Pesanan</h1>
        <p class="text-espresso-600 text-xs max-w-xs text-center mb-6 leading-relaxed">
            Periksa pesanan meja Anda di bawah ini dan selesaikan pembayaran via QRIS / E-Wallet / Virtual Account.
        </p>
    @endif

    <!-- Card Struk Digital Modern -->
    <div class="w-full bg-white rounded-3xl border border-cream-200 shadow-md shadow-espresso-950/5 overflow-hidden mb-5">
        <!-- Top Receipt Brand Bar -->
        <div class="bg-espresso-950 px-5 py-4 text-cream-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-600 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h3 class="text-xs font-extrabold tracking-tight text-white uppercase">Brader Cafe</h3>
                    <p class="text-[9px] text-cream-300">Struk Pesanan Digital</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-cream-400 font-mono block">Order ID</span>
                <span class="text-xs font-bold font-mono text-amber-400">{{ $order->order_number }}</span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="p-5 border-b border-cream-200/80 bg-cream-50/50 grid grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-espresso-500 tracking-wider block">Pelanggan</span>
                <span class="font-bold text-espresso-900 block truncate">{{ $order->customer_name }}</span>
                <span class="text-[11px] text-espresso-600 font-mono">{{ $order->customer_whatsapp }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-espresso-500 tracking-wider block">Lokasi Meja</span>
                <div class="inline-flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/30 text-amber-800 px-2.5 py-1 rounded-full font-extrabold text-xs mt-0.5">
                    <i class="fa-solid fa-chair text-[10px]"></i>
                    <span>Meja {{ $table->table_number }}</span>
                </div>
            </div>
        </div>

        <!-- Order Items Detail -->
        <div class="p-5 space-y-3">
            <span class="text-[10px] uppercase font-bold text-espresso-500 tracking-wider block mb-2">Rincian Menu</span>
            
            <div class="space-y-2.5 divide-y divide-cream-100">
                @foreach($order->items as $item)
                    <div class="pt-2 first:pt-0 flex items-start justify-between text-xs">
                        <div class="flex-grow pr-3">
                            <div class="font-extrabold text-espresso-950 flex items-center gap-1.5">
                                <span class="text-amber-700 font-black">{{ $item->quantity }}x</span>
                                <span>{{ $item->menu->name ?? 'Menu' }}</span>
                            </div>
                            @if($item->variant_name)
                                <span class="inline-block text-[10px] font-semibold text-amber-800 bg-amber-500/10 px-1.5 py-0.2 rounded mt-0.5">
                                    {{ $item->variant_name }}
                                </span>
                            @endif
                            @if($item->notes)
                                <p class="text-[10px] text-espresso-500 italic mt-0.5">"{{ $item->notes }}"</p>
                            @endif
                        </div>
                        <div class="text-right font-black text-espresso-950 shrink-0">
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Total Calculation -->
        <div class="p-5 bg-cream-100/60 border-t border-cream-200">
            <div class="flex items-center justify-between text-xs text-espresso-700 mb-1.5">
                <span>Subtotal Item</span>
                <span class="font-bold text-espresso-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-espresso-700 mb-3">
                <span>Biaya Layanan & Pajak</span>
                <span class="font-bold text-emerald-700">Rp 0 (Termasuk)</span>
            </div>
            <div class="pt-2.5 border-t border-cream-300 flex items-center justify-between">
                <div>
                    <span class="text-xs font-extrabold text-espresso-950 block">Total Pembayaran</span>
                    <span class="text-[10px] text-espresso-500">Harga final yang harus dibayar</span>
                </div>
                <span class="text-xl font-black text-amber-700">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Section Aksi Pembayaran Midtrans -->
    @if($order->status === 'pending_payment')
        <div class="w-full space-y-3 mb-6">
            @if(!empty($snapToken))
                <button id="pay-button" type="button" 
                        class="w-full bg-amber-600 hover:bg-amber-700 active:scale-[0.99] text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg shadow-amber-600/30 transition-all flex items-center justify-center gap-3 text-sm cursor-pointer">
                    <i class="fa-solid fa-qrcode text-lg"></i>
                    <span>Bayar Sekarang (Midtrans)</span>
                </button>

                <!-- Supported Payment Channels Icons -->
                <div class="bg-white rounded-2xl p-3.5 border border-cream-200 shadow-2xs">
                    <p class="text-[10px] font-bold text-espresso-500 uppercase tracking-wider text-center mb-2">
                        Pilihan Metode Pembayaran Otomatis
                    </p>
                    <div class="flex items-center justify-center gap-3 text-espresso-600 text-xs flex-wrap">
                        <span class="bg-cream-100 px-2.5 py-1 rounded-lg font-bold border border-cream-200">QRIS</span>
                        <span class="bg-cream-100 px-2.5 py-1 rounded-lg font-bold border border-cream-200">GoPay</span>
                        <span class="bg-cream-100 px-2.5 py-1 rounded-lg font-bold border border-cream-200">ShopeePay</span>
                        <span class="bg-cream-100 px-2.5 py-1 rounded-lg font-bold border border-cream-200">BCA VA</span>
                        <span class="bg-cream-100 px-2.5 py-1 rounded-lg font-bold border border-cream-200">BNI / Mandiri</span>
                    </div>
                </div>

                <!-- Real-time Polling Status Indicator -->
                <div class="flex items-center justify-center gap-2 text-[11px] text-espresso-500 py-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Sistem mendeteksi status pembayaran secara otomatis...</span>
                </div>
            @else
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-xs text-left">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mr-1.5"></i>
                    Token pembayaran Midtrans sedang dipersiapkan. Anda juga dapat langsung membayar tunai ke kasir dengan menyebutkan Order ID di atas.
                </div>
            @endif
        </div>
    @else
        <!-- Struk WhatsApp Delivery Card -->
        <div class="w-full p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-900 text-xs mb-6 text-left flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-brands fa-whatsapp text-lg"></i>
            </div>
            <div>
                <p class="font-extrabold text-sm mb-0.5">Struk Resmi Terkirim</p>
                <p class="text-emerald-800 leading-relaxed">
                    Bukti pembayaran otomatis telah dikirim ke nomor WhatsApp <strong class="font-bold">{{ $order->customer_whatsapp }}</strong>.
                </p>
            </div>
        </div>
    @endif

    <!-- Back to Menu Button -->
    <a href="{{ route('order.show', $token) }}" 
       class="inline-flex items-center gap-2 text-xs font-extrabold text-espresso-700 hover:text-amber-700 bg-white border border-cream-300/80 px-5 py-2.5 rounded-full shadow-2xs transition">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Pesan Menu Lainnya</span>
    </a>

</div>
@endsection

@push('scripts')
@if($order->status === 'pending_payment')
    @php
        $snapUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    @if(!empty($snapToken))
        <script src="{{ $snapUrl }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');
            const checkUrl = "{{ route('order.check_status', ['token' => $token, 'order' => $order->id]) }}";
            let isChecking = false;

            function checkPaymentStatus() {
                if (isChecking) return;
                isChecking = true;

                fetch(checkUrl, { cache: 'no-store' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_paid || data.status === 'paid') {
                            if (window.paymentInterval) clearInterval(window.paymentInterval);
                            window.location.reload();
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                        isChecking = false;
                    });
            }

            // Polling otomatis setiap 2 detik
            window.paymentInterval = setInterval(checkPaymentStatus, 2000);

            if (payButton && window.snap) {
                payButton.addEventListener('click', function () {
                    window.snap.pay('{{ $snapToken }}', {
                        onSuccess: function (result) {
                            checkPaymentStatus();
                            setTimeout(function () { window.location.reload(); }, 1000);
                        },
                        onPending: function (result) {
                            checkPaymentStatus();
                        },
                        onError: function (result) {
                            alert('Pembayaran gagal atau dibatalkan.');
                            checkPaymentStatus();
                        },
                        onClose: function () {
                            checkPaymentStatus();
                        }
                    });
                });
            }
        });
    </script>
@endif
@endpush
