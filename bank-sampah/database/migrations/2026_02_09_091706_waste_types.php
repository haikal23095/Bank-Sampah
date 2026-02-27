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
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('waste_categories')->onDelete('set null');
            $table->string('name');
            $table->decimal('price_per_kg', 12, 2);
            $table->enum('unit', ['kg', 'pcs', 'liter'])->default('kg');
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
