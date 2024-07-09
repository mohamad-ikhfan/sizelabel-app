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
        Schema::create('schedule_prints', function (Blueprint $table) {
            $table->id();
            $table->integer('line');
            $table->date('schedule')->nullable();
            $table->date('release');
            $table->string('style_number');
            $table->string('model_name');
            $table->float('qty');
            $table->foreignId('shoe_id')->nullable();
            $table->enum('status', ['printing', 'printed'])->nullable();
            $table->date('status_updated_at')->nullable();
            $table->foreignId('status_updated_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_prints');
    }
};