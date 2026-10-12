@extends('layouts.customer')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] px-6 text-center">
    <div class="bg-green-100 p-4 rounded-full mb-6">
        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
    <p class="text-gray-600 mb-8">
        Terima kasih, <span class="font-semibold">{{ $order->customer_name }}</span>.<br>
        Pesanan Anda sedang diproses. Silakan menuju kasir untuk melakukan pembayaran atau menunggu pesanan diantarkan ke meja Anda.
    </p>

    <div class="bg-white border border-gray-200 rounded-2xl w-full p-6 shadow-sm mb-8 text-left">
        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
            <span class="text-gray-500 text-sm">Nomor Order</span>
            <span class="font-bold text-gray-800">{{ $order->order_number }}</span>
        </div>
        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
            <span class="text-gray-500 text-sm">Meja</span>
            <span class="font-bold text-gray-800">{{ $table->table_number }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-500 text-sm">Total Bayar</span>
            <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <a href="{{ route('order.show', $token) }}" class="text-orange-600 font-semibold hover:text-orange-700">
        &larr; Kembali ke Menu
    </a>
</div>
@endsection

