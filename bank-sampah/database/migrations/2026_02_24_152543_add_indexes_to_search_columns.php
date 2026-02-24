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
        Schema::table('users', function (Blueprint $table) {
            $table->index('name');
            $table->index('phone');
        });

        Schema::table('waste_categories', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('waste_types', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['phone']);
        });

        Schema::table('waste_categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('waste_types', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
    }
};
