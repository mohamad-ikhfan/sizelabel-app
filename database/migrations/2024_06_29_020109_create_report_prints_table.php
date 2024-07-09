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
        Schema::create('report_prints', function (Blueprint $table) {
            $table->id();
            $table->date('print_date');
            $table->integer('line');
            $table->bigInteger('po_number')->nullable();
            $table->date('release');
            $table->string('style_number');
            $table->string('model_name')->nullable();
            $table->string('special');
            $table->float('qty_total');
            $table->string('remark');
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_prints');
    }
};