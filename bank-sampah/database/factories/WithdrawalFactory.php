<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Withdrawal;

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
            'amount' => $this->faker->randomFloat(2, 10000, 200000),
            'status' => $status,
            'method' => $this->faker->randomElement(['CASH', 'TRANSFER']),
            'admin_note' => $this->faker->optional()->sentence(),
        ];
    }
}
