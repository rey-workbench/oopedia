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
        Schema::create('mslq_answers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mslq_result_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('mslq_question_id')->constrained()->onDelete('cascade');
            $table->integer('value'); // 1-7
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mslq_answers');
    }
};
