<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppInvoice implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        $phone = $this->order->customer_whatsapp ?? $this->order->customer_phone;

        if (! $phone) {
            Log::warning('Cannot send WhatsApp invoice: Customer phone number is missing for order '.$this->order->id);

            return;
        }

        // Create the notification record in 'queued' state if not exists
        $notification = Notification::create([
            'order_id' => $this->order->id,
            'phone_number' => $phone,
            'status' => 'queued',
        ]);

        try {
            $success = $whatsappService->sendInvoice($phone, $this->order);

            $notification->update([
                'status' => $success ? 'sent' : 'failed',
                'sent_at' => $success ? now() : null,
            ]);
        } catch (\Exception $e) {
            $notification->update([
                'status' => 'failed',
            ]);
            Log::error('SendWhatsAppInvoice failed: '.$e->getMessage());
        }
    }
}
