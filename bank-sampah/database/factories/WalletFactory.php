<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

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
