<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function __construct(
        protected ?string $apiUrl = null,
        protected ?string $apiToken = null
    ) {
        $this->apiUrl = $apiUrl ?? config('services.whatsapp.url', env('WA_API_URL', 'https://api.fonnte.com'));
        $this->apiToken = $apiToken ?? config('services.whatsapp.token', env('WA_API_TOKEN', ''));
    }

    /**
     * Send an invoice to the given phone number for a specific order.
     */
    public function sendInvoice(string $phoneNumber, Order $order): bool
    {
        // Format the message
        $message = $this->formatMessage($order);

        try {
            $endpoint = rtrim($this->apiUrl, '/');
            if (! str_ends_with($endpoint, '/send')) {
                $endpoint .= '/send';
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->asForm()->post($endpoint, [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $responseData = $response->json();
            Log::info("WhatsApp Fonnte response for order {$order->order_number}:", [
                'status' => $response->status(),
                'body' => $responseData ?? $response->body(),
            ]);

            return $response->successful() && (($responseData['status'] ?? false) === true);
        } catch (\Exception $e) {
            Log::error('WhatsAppService Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Format the invoice message text.
     */
    protected function formatMessage(Order $order): string
    {
        $order->loadMissing(['table', 'items.menu']);

        $text = "*BRADER CAFE*\n";
        $text .= "-----------------------------------\n";
        $text .= "Nomor Pesanan: {$order->order_number}\n";
        $text .= "Tanggal/Waktu: {$order->created_at->format('d/m/Y H:i')}\n";
        $text .= 'Meja: '.($order->table ? $order->table->table_number : 'Takeaway')."\n";
        $text .= "Pelanggan: {$order->customer_name}\n";
        $text .= "-----------------------------------\n";

        foreach ($order->items as $item) {
            $variantText = ! empty($item->variant_name) ? " ({$item->variant_name})" : '';
            $menuName = $item->menu ? $item->menu->name : 'Item';
            $subtotal = $item->quantity * $item->price;
            $text .= "- {$menuName}{$variantText} x{$item->quantity} : Rp ".number_format($subtotal, 0, ',', '.')."\n";
            if (! empty($item->notes)) {
                $text .= "  Catatan: {$item->notes}\n";
            }
        }

        $text .= "-----------------------------------\n";
        $text .= 'Total Pembayaran: Rp '.number_format($order->total_amount, 0, ',', '.')."\n";
        $text .= "Status: *LUNAS*\n\n";
        $text .= 'Terima kasih atas pesanan Anda!';

        return $text;
    }
}
