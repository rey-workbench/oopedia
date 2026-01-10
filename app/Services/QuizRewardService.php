<?php

namespace App\Services;

use App\Repositories\ProgressRepository;

/**
 * QuizRewardService
 * 
 * Handles NON-ADAPTIVE rewards and tracking (applies uniformly to all users)
 * - XP and Points calculation
 * - Hint management
 * - Basic streak tracking
 * - Statistics updates
 */
class QuizRewardService
{
    protected $progressRepo;

    public function __construct(ProgressRepository $progressRepo)
    {
        $this->progressRepo = $progressRepo;
    }

    /**
     * Calculate rewards for a correct answer
     */
    public function calculateCorrectAnswerReward(array $state, bool $usedHint = false): array
    {
        $xpEarned = 10;
        $pointsEarned = 5;

        // Penalty for using hint
        if ($usedHint) {
            $xpEarned -= 5;
        }

        return [
            'xp_earned' => max(0, $xpEarned),
            'points_earned' => $pointsEarned,
            'updates' => [
                'xp' => ($state['xp'] ?? 0) + $xpEarned,
                'points' => ($state['points'] ?? 0) + $pointsEarned,
                'current_streak' => ($state['current_streak'] ?? 0) + 1,
                'wrong_streak' => 0,
                'total_questions_answered' => ($state['total_questions_answered'] ?? 0) + 1,
                'correct_count' => ($state['correct_count'] ?? 0) + 1,
            ]
        ];
    }

    /**
     * Update statistics for a wrong answer
     */
    public function processWrongAnswer(array $state): array
    {
        return [
            'xp_earned' => 0,
            'points_earned' => 0,
            'updates' => [
                'current_streak' => 0,
                'wrong_streak' => ($state['wrong_streak'] ?? 0) + 1,
                'total_questions_answered' => ($state['total_questions_answered'] ?? 0) + 1,
                'wrong_count' => ($state['wrong_count'] ?? 0) + 1,
            ]
        ];
    }

    /**
     * Process hint usage
     */
    public function useHint(array $state): array
    {
        $hintsAvailable = $state['hints_available'] ?? 0;

        if ($hintsAvailable <= 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada hint tersedia',
            ];
        }

        return [
            'success' => true,
            'message' => 'Hint digunakan',
            'updates' => [
                'hints_used' => ($state['hints_used'] ?? 0) + 1,
                'hints_available' => $hintsAvailable - 1,
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

    /**
     * Calculate accuracy percentage
     */
    public function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count'] ?? 0;
        $total = $state['total_questions_answered'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        return round(($correct / $total) * 100, 2);
    }

    /**
     * Get current state summary
     */
    public function getStateSummary(array $state): array
    {
        return [
            'xp' => $state['xp'] ?? 0,
            'points' => $state['points'] ?? 0,
            'total_questions' => $state['total_questions_answered'] ?? 0,
            'correct' => $state['correct_count'] ?? 0,
            'wrong' => $state['wrong_count'] ?? 0,
            'accuracy' => $this->calculateAccuracy($state),
            'current_streak' => $state['current_streak'] ?? 0,
            'hints_available' => $state['hints_available'] ?? 0,
        ];
    }
}
