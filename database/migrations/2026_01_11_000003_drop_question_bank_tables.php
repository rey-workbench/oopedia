<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop question_bank related tables (not needed for simplified structure).
     * Structure: 1 module → many materials → many questions
     */
    public function up(): void
    {
        Schema::dropIfExists('question_bank_configs');
        Schema::dropIfExists('question_bank_items');
        Schema::dropIfExists('question_banks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables if needed (copy from original migrations)
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->timestamps();
            
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });

        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_bank_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('question_bank_id')->references('id')->on('question_banks')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::create('question_bank_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_bank_id');
            $table->string('config_key');
            $table->text('config_value')->nullable();
            $table->timestamps();

            $table->foreign('question_bank_id')->references('id')->on('question_banks')->onDelete('cascade');
        });
    }
};
