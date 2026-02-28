<?php

namespace App\Services\Gamification;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\QuizRewardServiceInterface;

/**
 * QuizRewardService
 *
 * Handles XP and Points calculation, and Hint management.
 */
class QuizRewardService implements QuizRewardServiceInterface
{
    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function calculateCorrectAnswerReward(array $state, bool $usedHint = false, string $difficulty = 'beginner', int $timeSpent = 0): array
    {
        // Base XP based on difficulty
        $baseXp = match ($difficulty) {
            'medium' => 20,
            'hard' => 30,
            default => 10,
        };

        $xpEarned = $baseXp;

        // Speed bonus: +5 XP if answered in 20 seconds or less
        $isFast = $timeSpent > 0 && $timeSpent <= 20;
        if ($isFast) {
            $xpEarned += 5;
        }

        // Penalty for using hint: -5 XP
        if ($usedHint) {
            $xpEarned = max(0, $xpEarned - 5);
        }

        return [
            'global_xp_earned' => $xpEarned,
            'is_fast' => $isFast,
            'base_xp' => $baseXp,
            'updates' => [
                'global_xp' => ($state['global_xp'] ?? 0) + $xpEarned,
            ],
        ];
    }

    /**
     * Process wrong answer reward (zero XP)
     */
    public function processWrongAnswer(array $state): array
    {
        return [
            'global_xp_earned' => 0,
            'updates' => [
                'global_xp' => $state['global_xp'] ?? 0,
            ],
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
                'hints_used_count' => ($state['hints_used_count'] ?? 0) + 1,
                'hints_available' => $hintsAvailable - 1,
            ],
        ];
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
}
