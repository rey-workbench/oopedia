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
        // Drop if exists to ensure fresh start for this refactor
        Schema::dropIfExists('student_states');

        Schema::create('student_states', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();

            // JSON Data Structures
            $table->json('gamification_data')->nullable(); // global_xp, current_level, current_streak, max_streak, badges
            $table->json('learning_profile')->nullable(); // learning_style, mastery_levels, unlocked_modules
            $table->json('performance_metrics')->nullable(); // total_questions, correct_count, wrong_count, hints
            $table->json('adaptive_state')->nullable(); // fast_track, current_module, etc.

            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_states');
    }
};
