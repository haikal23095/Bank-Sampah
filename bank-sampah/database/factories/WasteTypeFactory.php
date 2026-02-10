<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WasteType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WasteType>
 */
class WasteTypeFactory extends Factory
{
    protected $model = WasteType::class;

    public function definition(): array
    {
        return [
            'category_id' => null,
            'name' => fake()->unique()->word() . ' ' . fake()->word(),
            'price_per_kg' => fake()->randomFloat(2, 0.5, 10.0),
            'unit' => 'kg',
        ];
    }
}
