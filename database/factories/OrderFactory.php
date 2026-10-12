<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.strtoupper(Str::random(8)),
            'table_id' => Table::factory(),
            'customer_name' => fake()->name(),
            'customer_whatsapp' => '08'.fake()->numerify('##########'),
            'total_amount' => 50000,
            'status' => 'pending_payment',
        ];
    }
}
