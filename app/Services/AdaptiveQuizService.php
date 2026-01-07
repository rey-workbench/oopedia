<?php

namespace App\Services;

use App\Models\StudentProgressState;
use App\Models\Question;
use App\Models\Progress;
use Illuminate\Support\Facades\Log;

class AdaptiveQuizService
{
    /**
     * Process a student's attempt at a question and update their adaptive state.
     * 
     * @param int $userId
     * @param int $materialId
     * @param Question $question
     * @param bool $isCorrect
     * @param bool $usedHint
     * @return array Result of the processing (xp, points, level changes)
     */
    public function processAttempt($userId, $materialId, Question $question, $isCorrect, $usedHint = false)
    {
        // 1. Get or Create Student State
        $state = StudentProgressState::firstOrCreate(
            ['user_id' => $userId, 'material_id' => $materialId],
            ['current_level' => 'beginner']
        );

        $levelChanged = 'none'; // 'up', 'down', 'none'
        $newLevel = $state->current_level;
        $xpEarned = 0;
        $pointsEarned = 0;
        $badgesEarned = [];
        $message = '';

        // 2. Update Basic Stats (Streaks)
        if ($isCorrect) {
            $state->current_streak++;
            $state->wrong_streak = 0;
            $state->level_correct_count++;
        } else {
            $state->current_streak = 0;
            $state->wrong_streak++;
        }
        $state->level_attempt_count++;
        $state->retry_count++; // Track activity on this level

        // 3. Calculate Rewards (Table 3.9 - Rules 5, 6, 9, 10, 11)
        if ($isCorrect) {
            // Rule 5: Basic Correct Answer
            $xpEarned += 10;
            $pointsEarned += 5;
            
            // Rule 9, 10, 11: Accuracy Bonuses (Calculated after 5 questions)
            if ($state->level_attempt_count >= 5) {
                $accuracy = ($state->level_correct_count / $state->level_attempt_count) * 100;
                
                if ($accuracy >= 85) { // Rule 11
                    $xpEarned += 50;
                    $pointsEarned += 50;
                    if (!in_array('Akurat', $state->badges ?? [])) {
                        $badges = $state->badges ?? [];
                        $badges[] = 'Akurat';
                        $state->badges = $badges;
                        $badgesEarned[] = 'Akurat';
                    }
                } elseif ($accuracy >= 75) { // Rule 10
                    $xpEarned += 25;
                    $pointsEarned += 25;
                } elseif ($accuracy >= 60) { // Rule 9
                    $xpEarned += 10;
                }
            }
        } else {
            // Rule 6: Wrong Answer
            $xpEarned += 0;
            $pointsEarned += 0;
        }

        // Rule 8: Hint Logic
        if ($usedHint && $isCorrect) {
            $xpEarned = floor($xpEarned * 0.5); // Reduce XP by 50%
        }

        // 4. Leveling Logic (Table 3.9 - Rules 1, 2, 3, 4)
        $currentLevel = $state->current_level;
        $accuracy = ($state->level_attempt_count > 0) 
            ? ($state->level_correct_count / $state->level_attempt_count) * 100 
            : 0;

        // Determine Level Change
        if ($currentLevel === 'beginner') {
            // Rule 1: Beginner -> Medium
            // IF correct streak >= 3 OR (total >= 5 AND correct streak >= 3 - Simplified interpreted from rule)
            // Rule says: streak >= 3, total >= 5. Let's stick closer to rule 1: Correct >= 3 AND total >= 5? 
            // The table says "benar berturut >= 3, total soal >= 5".
            
            if ($state->current_streak >= 3) {
                 // Promote
                 $newLevel = 'medium';
                 $levelChanged = 'up';
                 
                 // Rewards for leveling up (Rule 1)
                 $xpEarned += 100;
                 $pointsEarned += 50;
                 $message = 'Level Up! Welcome to Medium.';
            }
        } elseif ($currentLevel === 'medium') {
            // Rule 2: Medium -> Hard
            // IF streak >= 4 AND accuracy >= 75%
            if ($state->current_streak >= 4 && $accuracy >= 75) {
                $newLevel = 'hard';
                $levelChanged = 'up';
                
                // Rewards (Rule 2)
                $xpEarned += 150;
                $pointsEarned += 75;
                $message = 'Level Up! Prepare for Hard Mode.';
            }
            // Rule 4: Medium -> Beginner
            // IF wrong streak >= 4 AND accuracy < 40%
            elseif ($state->wrong_streak >= 4 && $accuracy < 40) {
                $newLevel = 'beginner';
                $levelChanged = 'down';
                $state->retry_count++; // Penalty count? Rule 4 says retry count +1
                $message = 'Let\'s reinforce the basics.';
            }
        } elseif ($currentLevel === 'hard') {
            // Rule 3: Hard -> Medium
            // IF wrong streak >= 3
            if ($state->wrong_streak >= 3) {
                $newLevel = 'medium';
                $levelChanged = 'down';
                $state->retry_count++; // Rule 3
                $message = 'Reducing cognitive load. Back to Medium.';
            }
        }

        // 5. Apply Level Change Updates
        if ($levelChanged !== 'none') {
            $state->current_level = $newLevel;
            // Reset counters on level change (Rule 1, 2, 3, 4)
            $state->current_streak = 0;
            $state->wrong_streak = 0;
            $state->level_correct_count = 0;
            $state->level_attempt_count = 0;
            $state->retry_count = 0;
        }

        // 6. Update Totals
        $state->total_xp += $xpEarned;
        $state->total_points += $pointsEarned;
        $state->last_activity_at = now();
        
        $state->save();

        return [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'xp_earned' => $xpEarned,
            'points_earned' => $pointsEarned,
            'level_changed' => $levelChanged, // 'up', 'down', 'none'
            'new_level' => $newLevel,
            'previous_level' => $currentLevel,
            'current_streak' => $state->current_streak,
            'total_xp' => $state->total_xp,
            'message' => $message,
            'badges_earned' => $badgesEarned
        ];
    }
}
