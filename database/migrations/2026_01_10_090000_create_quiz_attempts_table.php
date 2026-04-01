<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('question_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('answer_id')->nullable()->constrained()->nullOnDelete();
            $table->text('user_response')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('score')->default(0);
            $table->integer('attempt_number')->default(1);
            $table->integer('time_spent')->default(0)->comment('Time in seconds');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Index for faster "latest attempt" queries
            $table->index(['user_id', 'question_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
