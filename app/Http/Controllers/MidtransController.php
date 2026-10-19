<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppInvoice;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Handle webhook/callback notification from Midtrans.
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans callback received: ', $payload);

        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = (string) $request->input('fraud_status');
        $paymentType = (string) $request->input('payment_type');
        $transactionId = (string) $request->input('transaction_id');

        // 1. Validasi signature key dari Midtrans
        if (! $this->midtransService->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans callback signature verification failed for order: '.$orderId);

            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // 2. Cari order berdasarkan order_number
        $order = Order::where('order_number', $orderId)->first();
        if (! $order) {
            Log::info('Midtrans callback: Order not found for order_number '.$orderId.' (possibly a test notification)');

            return response()->json(['message' => 'Notification acknowledged (order not found)'], 200);
        }

        // 3. Tentukan status pembayaran untuk tabel payments
        $paymentStatus = match ($transactionStatus) {
            'capture' => ($fraudStatus === 'challenge') ? 'pending' : 'settlement',
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'deny',
            'expire' => 'expire',
            'cancel' => 'cancel',
            default => 'pending',
        };

        // Simpan / update ke tabel payments
        Payment::updateOrCreate(
            [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
            ],
            [
                'payment_type' => $paymentType,
                'gross_amount' => (float) $grossAmount,
                'status' => $paymentStatus,
                'raw_response' => $payload,
            ]
        );

        // 4. Update status pesanan di tabel orders
        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            if ($order->status !== 'paid') {
                $order->update(['status' => 'paid']);

                $order->statusLogs()->create([
                    'status' => 'paid',
                    'updated_by' => null, // Sistem otomatis via Midtrans
                ]);

                // 5. Otomatisasi WhatsApp: Kirim invoice saat pembayaran berhasil
                if ($order->customer_whatsapp || $order->customer_phone) {
                    SendWhatsAppInvoice::dispatch($order);
                }
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            if ($order->status === 'pending_payment') {
                $order->update(['status' => 'cancelled']);

                $order->statusLogs()->create([
                    'status' => 'cancelled',
                    'updated_by' => null,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Midtrans notification processed successfully',
        ]);
    }
}
