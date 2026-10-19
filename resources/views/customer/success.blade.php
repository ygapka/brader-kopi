@extends('layouts.customer')

@section('title', $order->status === 'paid' ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] px-4 py-8 text-center">
    
    @if($order->status === 'paid')
        <!-- Tampilan Jika Sudah Dibayar -->
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-5 shadow-sm">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mb-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Pembayaran Lunas
        </span>
        
        <h1 class="text-2xl font-black text-gray-900 mb-2">Pembayaran Berhasil!</h1>
        <p class="text-gray-600 text-sm max-w-sm mb-6">
            Terima kasih, <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>! Pembayaran Anda telah kami terima dan pesanan sedang segera disiapkan oleh barista.
        </p>
    @else
        <!-- Tampilan Jika Menunggu Pembayaran -->
        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mb-5 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-2">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            Menunggu Pembayaran
        </span>

        <h1 class="text-2xl font-black text-gray-900 mb-2">Selesaikan Pembayaran</h1>
        <p class="text-gray-600 text-sm max-w-sm mb-6">
            Halo <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>, silakan klik tombol di bawah untuk membayar via QRIS, GoPay, ShopeePay, atau Bank Transfer.
        </p>
    @endif

    <!-- Card Ringkasan Order -->
    <div class="bg-white border border-gray-200 rounded-2xl w-full p-5 shadow-sm mb-6 text-left space-y-3">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
            <span class="text-gray-500 text-xs uppercase font-medium tracking-wider">Nomor Order</span>
            <span class="font-bold font-mono text-gray-800">{{ $order->order_number }}</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
            <span class="text-gray-500 text-xs uppercase font-medium tracking-wider">Meja</span>
            <span class="font-bold text-gray-800">Meja {{ $table->table_number }}</span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
            <span class="text-gray-500 text-xs uppercase font-medium tracking-wider">Nomor WhatsApp</span>
            <span class="font-medium text-gray-800">{{ $order->customer_whatsapp }}</span>
        </div>
        
        <!-- Rincian Item Singkat -->
        <div class="py-2">
            <span class="text-gray-500 text-xs uppercase font-medium tracking-wider block mb-2">Rincian Menu</span>
            <div class="space-y-1.5">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-xs text-gray-700">
                        <span>{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }} {{ $item->variant_name ? '('.$item->variant_name.')' : '' }}</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
            <span class="text-gray-800 font-bold text-sm">Total Tagihan</span>
            <span class="font-black text-orange-600 text-xl">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Tombol Aksi Pembayaran -->
    @if($order->status === 'pending_payment')
        @if(!empty($snapToken))
            <button id="pay-button" type="button" 
                    class="w-full bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-orange-500/20 transition-all flex items-center justify-center gap-3 text-base mb-3 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Bayar Sekarang (Midtrans)</span>
            </button>
        @else
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs mb-3 text-left">
                Token pembayaran Midtrans sedang diproses atau terjadi gangguan koneksi. Silakan hubungi kasir untuk melakukan pembayaran manual.
            </div>
        @endif

        <p class="text-xs text-gray-500 mb-6">
            Anda juga dapat membayar secara langsung di kasir dengan menyebutkan <strong class="text-gray-700">Nomor Order</strong> di atas.
        </p>
    @else
        <div class="w-full p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs mb-6 text-left flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-bold mb-0.5">Struk Resmi Dikirimkan</p>
                <p class="text-emerald-700">Bukti pembayaran telah dikirimkan secara otomatis via WhatsApp ke nomor <span class="font-semibold">{{ $order->customer_whatsapp }}</span>.</p>
            </div>
        </div>
    @endif

    <a href="{{ route('order.show', $token) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-orange-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
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
                            // Hentikan interval dan otomatis reload ke halaman sukses/lunas
                            if (window.paymentInterval) clearInterval(window.paymentInterval);
                            window.location.reload();
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                        isChecking = false;
                    });
            }

            // Polling otomatis setiap 2 detik untuk mendeteksi pembayaran dari QRIS/Simulator
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
                            // Jika pop-up ditutup, langsung cek apakah user sudah selesai bayar
                            checkPaymentStatus();
                        }
                    });
                });
            }
        });
    </script>
@endif
@endpush
