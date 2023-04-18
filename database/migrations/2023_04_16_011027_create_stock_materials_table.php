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
        Schema::create('stock_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('material_id');
            $table->double('quantity');
            $table->date('date');
            $table->string('description')->nullable();
            $table->enum('status', ['in', 'out']);
            $table->double('first_stock');
            $table->double('last_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_materials');
    }
};