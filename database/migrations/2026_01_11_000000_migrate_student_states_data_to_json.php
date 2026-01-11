<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate existing data from individual columns to JSON structure.
     * Run this BEFORE the schema refactor migration.
     */
    public function up(): void
    {
        // Get all existing student states
        $states = DB::table('student_states')->get();
        
        foreach ($states as $state) {
            // Build JSON structures
            $gamificationData = [
                'global_xp' => $state->global_xp ?? 0,
                'current_level' => $state->current_level ?? 'Pemula',
                'current_streak' => $state->current_streak ?? 0,
                'max_streak' => $state->max_streak ?? 0,
                'badges' => json_decode($state->badges ?? '[]', true),
            ];
            
            $learningProfile = [
                'learning_style' => $state->learning_style ?? 'visual',
                'mastery_levels' => json_decode($state->mastery_levels ?? '{}', true),
                'unlocked_modules' => json_decode($state->unlocked_modules ?? '[]', true),
            ];
            
            $performanceMetrics = [
                'total_questions_answered' => $state->total_questions_answered ?? 0,
                'correct_count' => $state->correct_count ?? 0,
                'wrong_count' => $state->wrong_count ?? 0,
                'wrong_streak' => $state->wrong_streak ?? 0,
                'hints_used_count' => $state->hints_used_count ?? 0,
                'hints_available' => $state->hints_available ?? 3,
            ];
            
            $adaptiveState = [
                'fast_track_active' => false,
                'current_module_id' => null,
                'module_progress' => [],
                'time_metrics' => [
                    'avg_time_per_question' => 0,
                    'total_time_spent' => 0,
                ],
                'variables' => json_decode($state->adaptive_variables ?? '{}', true),
            ];
            
            // Update record with temporary JSON columns
            DB::table('student_states')
                ->where('id', $state->id)
                ->update([
                    'gamification_data' => json_encode($gamificationData),
                    'learning_profile' => json_encode($learningProfile),
                    'performance_metrics' => json_encode($performanceMetrics),
                    'adaptive_state' => json_encode($adaptiveState),
                ]);
        }
    }

    /**
     * Reverse the migration (restore from JSON to individual columns).
     */
    public function down(): void
    {
        $states = DB::table('student_states')->get();
        
        foreach ($states as $state) {
            $gamification = json_decode($state->gamification_data ?? '{}', true);
            $profile = json_decode($state->learning_profile ?? '{}', true);
            $metrics = json_decode($state->performance_metrics ?? '{}', true);
            
            DB::table('student_states')
                ->where('id', $state->id)
                ->update([
                    'global_xp' => $gamification['global_xp'] ?? 0,
                    'current_level' => $gamification['current_level'] ?? 'Pemula',
                    'current_streak' => $gamification['current_streak'] ?? 0,
                    'max_streak' => $gamification['max_streak'] ?? 0,
                    'badges' => json_encode($gamification['badges'] ?? []),
                    'learning_style' => $profile['learning_style'] ?? null,
                    'mastery_levels' => json_encode($profile['mastery_levels'] ?? []),
                    'unlocked_modules' => json_encode($profile['unlocked_modules'] ?? []),
                    'total_questions_answered' => $metrics['total_questions_answered'] ?? 0,
                    'correct_count' => $metrics['correct_count'] ?? 0,
                    'wrong_count' => $metrics['wrong_count'] ?? 0,
                    'wrong_streak' => $metrics['wrong_streak'] ?? 0,
                    'hints_used_count' => $metrics['hints_used_count'] ?? 0,
                    'hints_available' => $metrics['hints_available'] ?? 3,
                ]);
        }
    }
};
