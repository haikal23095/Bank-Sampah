<?php

namespace Database\Factories;

use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['PENDING', 'SUCCESS', 'FAILED']);

        return [
            'user_id' => null,
            'staff_id' => null,
            'date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            // Amount in Rupiah (integer), using multiples of 1000 and minimum 1000
            'amount' => $this->faker->numberBetween(1, 200) * 1000,
            'status' => $status,
            'method' => $this->faker->randomElement(['CASH', 'TRANSFER']),
            'admin_note' => $this->faker->optional()->sentence(),
        ];
    }
}
