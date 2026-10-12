<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuVariant>
 */
class MenuVariantFactory extends Factory
{
    protected $model = MenuVariant::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'name' => fake()->randomElement(['Less Sugar', 'No Sugar', 'Extra Shot', 'Oat Milk', 'Extra Ice', 'Hot']),
            'extra_price' => fake()->randomElement([0, 2000, 3000, 5000]),
        ];
    }
}
