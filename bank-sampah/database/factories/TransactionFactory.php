<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['DEPOSIT', 'WITHDRAWAL']);
        $status = $this->faker->randomElement(['PENDING', 'SUCCESS', 'FAILED']);

        return [
            'user_id' => null,
            'staff_id' => null,
            'date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'type' => $type,
            'total_amount' => 0,
            'total_weight' => 0,
            'status' => $status,
            'method' => $this->faker->randomElement(['CASH', 'TRANSFER']),
            'admin_note' => null,
        ];
    }
}
