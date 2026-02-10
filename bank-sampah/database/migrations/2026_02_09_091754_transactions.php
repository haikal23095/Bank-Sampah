<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Nasabah
            $table->foreignId('staff_id')->nullable()->constrained('users'); // Petugas yang melayani
            $table->date('date');
            $table->enum('type', ['DEPOSIT', 'WITHDRAWAL']);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_weight', 10, 2)->nullable(); // Hanya untuk deposit
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING');
            $table->enum('method', ['CASH', 'TRANSFER'])->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
