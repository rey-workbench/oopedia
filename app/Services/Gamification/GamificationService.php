<?php

namespace App\Services\Gamification;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * GamificationService
 *
 * Unified service for all gamification logic: XP rewards, streaks, and leveling.
 * Consolidates the former QuizRewardService, StreakService, and LevelingService.
 */
class GamificationService implements GamificationServiceInterface
{
    // ==================== XP CONSTANTS ====================
    /** Base XP per difficulty level */
    private const BASE_XP = ['beginner' => 10, 'medium' => 20, 'hard' => 30];

    /** Extra XP for answering within TIME_FAST_THRESHOLD % of allocated time */
    private const FAST_BONUS_XP = 5;

    /** XP deducted for using a hint */
    private const HINT_PENALTY_XP = 5;

    // ==================== LEVEL THRESHOLDS ====================
    /**
     * Single source of truth for level thresholds.
     * Entries must be ordered from lowest to highest XP minimum.
     */
    private const LEVELS = [
        ['name' => 'Pemula', 'min' => 0],
        ['name' => 'Menengah', 'min' => 50],
        ['name' => 'Mahir', 'min' => 200],
        ['name' => 'Ahli', 'min' => 500],
    ];

    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    // ==================== REWARD ====================

    public function calculateCorrectAnswerReward(array $state, bool $usedHint = false, string $difficulty = 'beginner', int $timeSpent = 0): array
    {
        $baseXp   = self::BASE_XP[$difficulty] ?? self::BASE_XP['beginner'];
        $xpEarned = $baseXp;

        // Speed bonus
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$difficulty] ?? 60;
        $isFast        = $timeSpent > 0 && ($timeSpent / $allocatedTime * 100) < AdaptiveConstants::TIME_FAST_THRESHOLD;
        if ($isFast) {
            $xpEarned += self::FAST_BONUS_XP;
        }

        // Penalty for using hint
        if ($usedHint) {
            $xpEarned = max(0, $xpEarned - self::HINT_PENALTY_XP);
        }

        return [
            'global_xp_earned' => $xpEarned,
            'is_fast'          => $isFast,
            'base_xp'          => $baseXp,
            'updates'          => [
                'global_xp' => ($state['global_xp'] ?? 0) + $xpEarned,
            ],
        ];
    }

    public function processWrongAnswer(array $state): array
    {
        return [
            'global_xp_earned' => 0,
            'updates'          => [
                'global_xp' => $state['global_xp'] ?? 0,
            ],
        ];
    }

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
                'hints_available'  => $hintsAvailable - 1,
            ],
        ];
    }

    public function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count']            ?? 0;
        $total   = $state['total_questions_answered'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        return round(($correct / $total) * 100, 2);
    }

    // ==================== STREAK ====================

    public function updateCorrectStreak(array $state): array
    {
        return [
            'updates' => [
                'current_streak' => ($state['current_streak'] ?? 0) + 1,
                'wrong_streak'   => 0,
            ],
        ];
    }

    public function updateWrongStreak(array $state): array
    {
        return [
            'updates' => [
                'current_streak' => 0,
                'wrong_streak'   => ($state['wrong_streak'] ?? 0) + 1,
            ],
        ];
    }

    public function checkStreakBonus(array $state): ?array
    {
        $currentStreak = $state['current_streak'] ?? 0;

        if ($currentStreak > 0 && $currentStreak % 5 === 0) {
            return [
                'bonus_granted' => true,
                'message'       => "Streak {$currentStreak}! +1 Hint bonus",
                'updates'       => [
                    'hints_available' => ($state['hints_available'] ?? 0) + 1,
                ],
            ];
        }

        return null;
    }

    public function calculateStreakBonusXP(int $currentStreak): int
    {
        if ($currentStreak >= 10) {
            return 20;
        }
        if ($currentStreak >= 5) {
            return 10;
        }
        if ($currentStreak >= 3) {
            return 5;
        }

        return 0;
    }

    // ==================== LEVELING ====================

    public function determineLevel(int $xp): string
    {
        $currentLevel = 'Pemula';

        foreach (self::LEVELS as $level) {
            if ($xp >= $level['min']) {
                $currentLevel = $level['name'];
            }
        }

        return $currentLevel;
    }

    public function getLevelProgress(int $xp): array
    {
        $levels       = self::LEVELS;
        $currentLevel = $this->determineLevel($xp);
        $currentIndex = 0;

        foreach ($levels as $index => $level) {
            if ($level['name'] === $currentLevel) {
                $currentIndex = $index;
                break;
            }
        }

        $nextLevel = $levels[$currentIndex + 1] ?? null;

        if (! $nextLevel) {
            return [
                'current_level' => $currentLevel,
                'next_level'    => null,
                'percentage'    => 100,
                'xp_needed'     => 0,
            ];
        }

        $currentMin    = $levels[$currentIndex]['min'];
        $nextMin       = $nextLevel['min'];
        $progressXp    = $xp      - $currentMin;
        $totalXpNeeded = $nextMin - $currentMin;

        return [
            'current_level' => $currentLevel,
            'next_level'    => $nextLevel['name'],
            'percentage'    => round(($progressXp / $totalXpNeeded) * 100),
            'xp_needed'     => $nextMin - $xp,
        ];
    }
}
