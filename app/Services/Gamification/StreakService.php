<?php

namespace App\Services\Gamification;

/**
 * StreakService
 * 
 * Handles streak tracking and streak-based bonuses.
 */
class StreakService
{
    /**
     * Update streak for a correct answer
     */
    public function updateCorrectStreak(array $state): array
    {
        return [
            'updates' => [
                'current_streak' => ($state['current_streak'] ?? 0) + 1,
                'wrong_streak' => 0,
            ]
        ];
    }

    /**
     * Update streak for a wrong answer
     */
    public function updateWrongStreak(array $state): array
    {
        return [
            'updates' => [
                'current_streak' => 0,
                'wrong_streak' => ($state['wrong_streak'] ?? 0) + 1,
            ]
        ];
    }

    /**
     * Award bonus hints based on streak
     */
    public function checkStreakBonus(array $state): ?array
    {
        $currentStreak = $state['current_streak'] ?? 0;

        // Every 5 correct answers in a row = 1 bonus hint
        if ($currentStreak > 0 && $currentStreak % 5 === 0) {
            return [
                'bonus_granted' => true,
                'message' => "Streak {$currentStreak}! +1 Hint bonus",
                'updates' => [
                    'hints_available' => ($state['hints_available'] ?? 0) + 1,
                ]
            ];
        }

        return null;
    }
}
