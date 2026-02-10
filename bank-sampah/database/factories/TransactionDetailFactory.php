<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TransactionDetail;
use App\Models\WasteType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    protected $model = TransactionDetail::class;

    public function definition(): array
    {
        $wasteType = WasteType::inRandomOrder()->first();
        if (! $wasteType) {
            $wasteType = WasteType::factory()->create();
        }

        $weight = fake()->randomFloat(2, 0.1, 20);
        $price = $wasteType->price_per_kg ?? 1.0;
        $subtotal = round($weight * $price, 2);

        return [
            'transaction_id' => null,
            'waste_type_id' => $wasteType->id,
            'weight' => $weight,
            'subtotal' => $subtotal,
        ];
    }
}
