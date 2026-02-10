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
        $type = fake()->randomElement(['deposit', 'withdrawal']);
        $status = fake()->randomElement(['pending', 'completed', 'failed']);

        return [
            'user_id' => null,
            'staff_id' => null,
            'date' => fake()->dateTimeBetween('-1 years', 'now'),
            'type' => $type,
            'total_amount' => 0,
            'total_weight' => 0,
            'status' => $status,
            'method' => fake()->randomElement(['cash', 'transfer']),
            'admin_note' => null,
        ];
    }
}
