<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Indeks komposit untuk mempercepat pengecekan duplikasi transaksi
            // yang menggunakan filter user_id, staff_id, amount, method, dan created_at
            $table->index(['user_id', 'staff_id', 'amount', 'method', 'created_at'], 'withdrawals_duplicate_check_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropIndex('withdrawals_duplicate_check_index');
        });
    }
};
