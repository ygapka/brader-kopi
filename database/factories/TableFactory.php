<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        static $number = 1;

        return [
            'table_number' => (string) ($number++),
            'token' => Str::random(16),
            'is_active' => true,
        ];
    }
}
