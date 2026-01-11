<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Refactor student_states table to use JSON columns for cleaner schema.
     * Consolidates 20+ individual columns into 3 main JSON columns.
     */
    public function up(): void
    {
        Schema::table('student_states', function (Blueprint $table) {
            // Drop old individual columns (will be migrated to JSON)
            $table->dropColumn([
                'global_xp',
                'current_level',
                'current_streak',
                'max_streak',
                'learning_style',
                'mastery_levels',
                'adaptive_variables',
                'badges',
                'unlocked_modules',
                'total_questions_answered',
                'correct_count',
                'wrong_count',
                'wrong_streak',
                'hints_used_count',
                'hints_available',
            ]);
            
            // Add new consolidated JSON columns
            $table->json('gamification_data')->nullable()->after('user_id')->comment(
                'XP, level, streaks, badges: {global_xp, current_level, current_streak, max_streak, badges: []}'
            );
            
            $table->json('learning_profile')->nullable()->after('gamification_data')->comment(
                'Learning preferences & mastery: {learning_style, mastery_levels: {}, unlocked_modules: []}'
            );
            
            $table->json('performance_metrics')->nullable()->after('learning_profile')->comment(
                'Question stats & hints: {total_questions_answered, correct_count, wrong_count, wrong_streak, hints_used_count, hints_available}'
            );
            
            $table->json('adaptive_state')->nullable()->after('performance_metrics')->comment(
                'Dynamic adaptive variables: {fast_track_active, current_module_id, module_progress: {}, time_metrics: {}}'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_states', function (Blueprint $table) {
            // Drop JSON columns
            $table->dropColumn([
                'gamification_data',
                'learning_profile',
                'performance_metrics',
                'adaptive_state',
            ]);
            
            // Restore old individual columns
            $table->integer('global_xp')->default(0);
            $table->string('current_level')->default('Pemula');
            $table->integer('current_streak')->default(0);
            $table->integer('max_streak')->default(0);
            $table->string('learning_style')->nullable();
            $table->json('mastery_levels')->nullable();
            $table->json('adaptive_variables')->nullable();
            $table->json('badges')->nullable();
            $table->json('unlocked_modules')->nullable();
            $table->integer('total_questions_answered')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('wrong_streak')->default(0);
            $table->integer('hints_used_count')->default(0);
            $table->integer('hints_available')->default(3);
        });
    }
};
