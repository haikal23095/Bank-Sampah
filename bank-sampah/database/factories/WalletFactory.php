<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Wallet;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            // Balance in Rupiah (integer-like), using multiples of 1000
            'balance' => (float) (fake()->numberBetween(0, 500) * 1000),
        ];
    }
}
