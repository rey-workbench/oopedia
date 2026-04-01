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
        Schema::dropIfExists('student_states');

        Schema::create('student_states', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();

            // gamification_data: { global_xp: int, current_level: string, current_streak: int, max_streak: int, badges: array }
            $table->json('gamification_data')->nullable();

            // learning_profile: { learning_style: string, unlocked_modules: array, certifications: array }
            $table->json('learning_profile')->nullable();

            // performance_metrics: { total_questions_answered: int, correct_count: int, wrong_count: int, wrong_streak: int, hints_used_count: int, hints_available: int }
            $table->json('performance_metrics')->nullable();

            // adaptive_state: { fast_track_active: bool, current_material_id: string|null, target_difficulty: string|null, module_progress: object, time_metrics: { avg_time_per_question: int, total_time_spent: int } }
            $table->json('adaptive_state')->nullable();

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
