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
        Schema::create('sus_questions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->integer('order')->unique();
            $table->string('text');
            $table->boolean('is_reverse')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sus_questions');
    }
};
