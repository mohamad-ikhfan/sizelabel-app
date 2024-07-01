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
        Schema::create('loadplans', function (Blueprint $table) {
            $table->id();
            $table->integer('line');
            $table->date('spk_publish')->nullable();
            $table->date('release');
            $table->date('doc_date')->nullable();
            $table->bigInteger('po_number')->nullable();
            $table->string('style_number');
            $table->string('model_name');
            $table->string('invoice')->nullable();
            $table->string('destination')->nullable();
            $table->date('ogac')->nullable();
            $table->float('qty_origin');
            $table->string('special');
            $table->string('remark');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadplans');
    }
};