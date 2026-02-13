<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('wallets')) {
            return;
        }

        // Multiply existing balances by 1000 to convert to Rupiah
        DB::statement('UPDATE wallets SET balance = ROUND(balance * 1000, 2) WHERE balance IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('wallets')) {
            return;
        }

        // Revert by dividing by 1000 (best-effort)
        DB::statement('UPDATE wallets SET balance = ROUND(balance / 1000, 2) WHERE balance IS NOT NULL');
    }
};
