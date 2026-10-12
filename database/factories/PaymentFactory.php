<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'transaction_id' => 'MIDTRANS-'.Str::uuid(),
            'payment_type' => 'qris',
            'gross_amount' => 50000,
            'status' => 'settlement',
            'raw_response' => [
                'status_code' => '200',
                'status_message' => 'Success, transaction is found',
            ],
        ];
    }
}
