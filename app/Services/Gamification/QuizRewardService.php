<?php

namespace App\Services\Gamification;

use App\Repositories\ProgressRepository;

/**
 * QuizRewardService
 * 
 * Handles XP and Points calculation, and Hint management.
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

        // Penalty for using hint
        if ($usedHint) {
            $xpEarned -= 5;
        }

        return [
            'global_xp_earned' => max(0, $xpEarned),
            'updates' => [
                'global_xp' => ($state['global_xp'] ?? 0) + $xpEarned,
            ]
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
                'hints_used_count' => ($state['hints_used_count'] ?? 0) + 1,
                'hints_available' => $hintsAvailable - 1,
            ]
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
