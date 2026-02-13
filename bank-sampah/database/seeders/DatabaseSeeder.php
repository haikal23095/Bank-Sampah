<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteType;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create waste categories and types
        $categories = WasteCategory::factory()->count(5)->create();
        foreach ($categories as $cat) {
            WasteType::factory()->count(6)->create([
                'category_id' => $cat->id,
            ]);
        }

        // Create a known test user first (avoid unique collisions)
        $testUser = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'join_date' => now()->toDateString(),
        ]);

        // Create a default admin account for local/dev use
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'join_date' => now()->toDateString(),
        ]);

        // Create other users
        $users = User::factory()->count(20)->create();

        // Create some staff users
        $staffUsers = User::factory()->count(3)->create([
            'role' => 'PETUGAS',
        ]);

        // Create wallets for all users (including test user)
        $allUsers = $users->concat([$testUser])->concat($staffUsers);
        foreach ($allUsers as $user) {
            Wallet::updateOrCreate([
                'user_id' => $user->id,
            ], [
                // Initial balance in Rupiah (multiples of 1000)
                'balance' => fake()->numberBetween(0, 500) * 1000,
        $wasteTypes = WasteType::all();
        if ($wasteTypes->isEmpty()) {
            $wasteTypes = WasteType::factory()->count(10)->create();
        }

        // Seed transactions and details — wrapped in try/catch because existing
        // database schema may differ. If it fails, we skip this step.
        try {
            $transactionsCount = 120;
            for ($i = 0; $i < $transactionsCount; $i++) {
                $user = $allUsers->random();
                $staff = $staffUsers->random();

                $transaction = Transaction::factory()->create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'date' => fake()->dateTimeBetween('-1 years', 'now'),
                ]);

                $detailCount = rand(1, 4);
                for ($d = 0; $d < $detailCount; $d++) {
                    $wt = $wasteTypes->random();
                    $weight = fake()->randomFloat(2, 0.1, 20);
                    $subtotal = round($weight * $wt->price_per_kg, 2);

                    TransactionDetail::factory()->create([
                        'transaction_id' => $transaction->id,
                        'waste_type_id' => $wt->id,
                        'weight' => $weight,
                        'subtotal' => $subtotal,
                    ]);
                }

                // Recalculate totals (derived from details)
                $totalAmount = TransactionDetail::where('transaction_id', $transaction->id)->sum('subtotal');

                // Update wallet balance for deposit/withdrawal
                $wallet = Wallet::where('user_id', $user->id)->first();
                if ($wallet) {
                    $wallet->increment('balance', $totalAmount);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Skipping transactions seeding: ' . $e->getMessage());
        }
    }
}
