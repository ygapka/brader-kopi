<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'phone_number' => '08'.fake()->numerify('##########'),
            'status' => 'sent',
            'sent_at' => now(),
        ];
    }
}
