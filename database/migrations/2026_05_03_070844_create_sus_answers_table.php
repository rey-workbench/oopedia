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
        Schema::create('sus_answers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sus_result_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('sus_question_id')->constrained()->onDelete('cascade');
            $table->integer('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sus_answers');
    }
};
