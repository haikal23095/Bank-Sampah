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
        Schema::table('transactions', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('transactions', 'type')) {
                $columns[] = 'type';
            }
            if (Schema::hasColumn('transactions', 'total_amount')) {
                $columns[] = 'total_amount';
            }
            if (Schema::hasColumn('transactions', 'total_weight')) {
                $columns[] = 'total_weight';
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $columns[] = 'status';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'type')) {
                $table->enum('type', ['DEPOSIT', 'WITHDRAWAL'])->after('date');
            }
            if (!Schema::hasColumn('transactions', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->after('type');
            }
            if (!Schema::hasColumn('transactions', 'total_weight')) {
                $table->decimal('total_weight', 10, 2)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING')->after('total_weight');
            }
        });
    }
};
