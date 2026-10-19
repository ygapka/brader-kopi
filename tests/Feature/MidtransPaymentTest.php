<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppInvoice;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'midtrans.server_key' => 'test-server-key',
            'midtrans.client_key' => 'test-client-key',
            'midtrans.merchant_id' => 'test-merchant',
            'midtrans.is_production' => false,
        ]);
    }

    public function test_callback_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => 'ORD-12345678',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => 'invalid-signature-hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid signature key']);
    }

    public function test_callback_handles_unknown_order_gracefully(): void
    {
        $orderId = 'ORD-UNKNOWN';
        $statusCode = '200';
        $grossAmount = '50000.00';
        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key');

        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200);
    }

    public function test_callback_updates_order_to_paid_and_dispatches_whatsapp_invoice(): void
    {
        Queue::fake();

        $table = Table::create([
            'table_number' => '01',
            'token' => 'table-test-token',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST1234',
            'table_id' => $table->id,
            'customer_name' => 'Budi Santoso',
            'customer_whatsapp' => '081234567890',
            'total_amount' => 50000.00,
            'status' => 'pending_payment',
        ]);

        $orderId = $order->order_number;
        $statusCode = '200';
        $grossAmount = '50000.00';
        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key');

        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'transaction_id' => 'trx-midtrans-999',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);

        // Verify order is updated to paid
        $order->refresh();
        $this->assertEquals('paid', $order->status);

        // Verify payment record is created
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'transaction_id' => 'trx-midtrans-999',
            'payment_type' => 'qris',
            'status' => 'settlement',
        ]);

        // Verify WhatsApp invoice job was dispatched
        Queue::assertPushed(SendWhatsAppInvoice::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    }

    public function test_callback_updates_order_to_cancelled_on_expire(): void
    {
        $table = Table::create([
            'table_number' => '02',
            'token' => 'table-test-token-2',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-EXPIRE123',
            'table_id' => $table->id,
            'customer_name' => 'Siti Nurhaliza',
            'customer_whatsapp' => '081234567891',
            'total_amount' => 25000.00,
            'status' => 'pending_payment',
        ]);

        $orderId = $order->order_number;
        $statusCode = '202';
        $grossAmount = '25000.00';
        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key');

        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'expire',
            'transaction_id' => 'trx-midtrans-expired',
            'payment_type' => 'gopay',
        ]);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_customer_checkout_and_success_flow(): void
    {
        $mockMidtrans = $this->mock(MidtransService::class);
        $mockMidtrans->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('dummy-snap-token-abc12345');

        $table = Table::create([
            'table_number' => '03',
            'token' => 'table-token-03',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Kopi Susu Gula Aren',
            'price' => 18000,
            'is_available' => true,
        ]);

        $postData = [
            'customer_name' => 'Ahmad Dani',
            'customer_whatsapp' => '081299887766',
            'items' => json_encode([
                [
                    'menu_id' => $menu->id,
                    'variant_name' => null,
                    'quantity' => 2,
                    'notes' => 'Sedikit es',
                ],
            ]),
        ];

        $checkoutResponse = $this->post("/order/{$table->token}/checkout", $postData);

        $order = Order::where('customer_name', 'Ahmad Dani')->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending_payment', $order->status);
        $this->assertEquals('dummy-snap-token-abc12345', $order->snap_token);
        $this->assertEquals(36000.00, (float) $order->total_amount);

        $checkoutResponse->assertRedirect(route('order.success', [
            'token' => $table->token,
            'order' => $order->id,
        ]));

        // View success page when pending_payment
        $successResponse = $this->get(route('order.success', [
            'token' => $table->token,
            'order' => $order->id,
        ]));

        $successResponse->assertStatus(200);
        $successResponse->assertSee('Bayar Sekarang (Midtrans)');
        $successResponse->assertSee('dummy-snap-token-abc12345');

        // View success page when paid
        $order->update(['status' => 'paid']);
        $paidResponse = $this->get(route('order.success', [
            'token' => $table->token,
            'order' => $order->id,
        ]));

        $paidResponse->assertStatus(200);
        $paidResponse->assertSee('Pembayaran Berhasil!');
        $paidResponse->assertSee('Pembayaran Lunas');
        $paidResponse->assertDontSee('Bayar Sekarang (Midtrans)');
    }
}
