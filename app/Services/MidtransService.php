<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    /**
     * Generate Snap Token for an Order.
     */
    public function getSnapToken(Order $order): ?string
    {
        $order->loadMissing(['items.menu', 'table']);

        $itemDetails = [];
        foreach ($order->items as $item) {
            $name = $item->menu ? $item->menu->name : 'Item';
            if (! empty($item->variant_name)) {
                $name .= ' - '.$item->variant_name;
            }

            $itemDetails[] = [
                'id' => (string) $item->id,
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($name, 0, 50),
            ];
        }

        $grossAmount = (int) round((float) $order->total_amount);
        $itemsTotal = array_sum(array_map(fn (array $item): int => $item['price'] * $item['quantity'], $itemDetails));

        // If items sum doesn't match total_amount, use consolidated item to prevent Midtrans validation failure
        if ($itemsTotal !== $grossAmount || empty($itemDetails)) {
            $itemDetails = [
                [
                    'id' => 'ORDER-'.$order->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => 'Pesanan '.$order->order_number,
                ],
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_whatsapp ?: $order->customer_phone,
            ],
            'item_details' => $itemDetails,
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('Midtrans getSnapToken error: '.$e->getMessage(), [
                'order_number' => $order->order_number,
                'params' => $params,
            ]);

            return null;
        }
    }

    /**
     * Validate the webhook signature from Midtrans.
     * Formula: sha512(order_id + status_code + gross_amount + ServerKey)
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = (string) config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }
}
