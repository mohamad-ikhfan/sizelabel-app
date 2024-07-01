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
        Schema::create('shoes', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('gender');
            $table->string('group_size');
            $table->string('material');
            $table->string('measure');
            $table->boolean('wide')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoes');
    }
};
