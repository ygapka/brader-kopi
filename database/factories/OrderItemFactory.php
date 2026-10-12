<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'menu_id' => Menu::factory(),
            'variant_name' => fake()->optional()->randomElement(['Less Sugar', 'Extra Shot']),
            'quantity' => fake()->numberBetween(1, 3),
            'price' => 25000,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
