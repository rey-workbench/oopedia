<?php

declare(strict_types=1);

namespace App\Services\Gamification;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Models\Question;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Schemas\StudentStateSchema;

/**
 * GamificationService
 *
 * Unified service for all gamification logic: XP rewards, streaks, and leveling.
 * Consolidates the former QuizRewardService, StreakService, and LevelingService.
 */
final class GamificationService implements GamificationServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
    ) {
    }

    // ==================== REWARD ====================

    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        string $difficulty = Question::DIFFICULTY_BEGINNER,
        int $timeSpent = 0,
    ): array {
        $baseXp = match ($difficulty) {
            Question::DIFFICULTY_MEDIUM => StudentStateSchema::XP_REWARD_MEDIUM,
            Question::DIFFICULTY_HARD   => StudentStateSchema::XP_REWARD_HARD,
            Question::DIFFICULTY_FINAL  => StudentStateSchema::XP_REWARD_FINAL,
            default                     => StudentStateSchema::XP_REWARD_BEGINNER,
        };
        $xpEarned = $baseXp;

        // Speed bonus
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$difficulty] ?? 60;
        $isFast        = $timeSpent > 0 && ($timeSpent / $allocatedTime * 100) < AdaptiveConstants::TIME_FAST_THRESHOLD;
        if ($isFast) {
            $xpEarned += StudentStateSchema::XP_BONUS_FAST;
        }

        // Penalty for using hint
        if ($usedHint) {
            $xpEarned = max(0, $xpEarned - StudentStateSchema::XP_PENALTY_HINT);
        }

        return [
            'global_xp_earned' => $xpEarned,
            'is_fast'          => $isFast,
            'base_xp'          => $baseXp,
            'updates'          => [
                StudentStateSchema::KEY_GLOBAL_XP => ($state[StudentStateSchema::KEY_GLOBAL_XP] ?? 0) + $xpEarned,
            ],
        ];
    }

    public function processWrongAnswer(array $state): array
    {
        return [
            'global_xp_earned' => 0,
            'updates'          => [
                StudentStateSchema::KEY_GLOBAL_XP => $state[StudentStateSchema::KEY_GLOBAL_XP] ?? 0,
            ],
        ];
    }

    public function useHint(array $state): array
    {
        $hintsAvailable = $state[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? 0;

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
                StudentStateSchema::KEY_HINTS_USED_COUNT => ($state[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0) + 1,
                StudentStateSchema::KEY_HINTS_AVAILABLE  => $hintsAvailable - 1,
            ],
        ];
    }

    public function calculateAccuracy(array $state): float
    {
        $correct = $state[StudentStateSchema::KEY_CORRECT_COUNT]            ?? 0;
        $total   = $state[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0;

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
                StudentStateSchema::KEY_CURRENT_STREAK => ($state[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0) + 1,
                StudentStateSchema::KEY_WRONG_STREAK   => 0,
            ],
        ];
    }

    public function updateWrongStreak(array $state): array
    {
        return [
            'updates' => [
                StudentStateSchema::KEY_CURRENT_STREAK => 0,
                StudentStateSchema::KEY_WRONG_STREAK   => ($state[StudentStateSchema::KEY_WRONG_STREAK] ?? 0) + 1,
            ],
        ];
    }

    public function checkStreakBonus(array $state): ?array
    {
        $currentStreak = $state[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0;

        if ($currentStreak > 0 && $currentStreak % StudentStateSchema::STREAK_HINT_THRESHOLD === 0) {
            return [
                'bonus_granted' => true,
                'message'       => "Streak {$currentStreak}! +1 Hint bonus",
                'updates'       => [
                    StudentStateSchema::KEY_HINTS_AVAILABLE => ($state[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? 0) + 1,
                ],
            ];
        }

        return null;
    }

    public function calculateStreakBonusXP(int $currentStreak): int
    {
        foreach (StudentStateSchema::STREAK_XP_BONUSES as $threshold => $bonus) {
            if ($currentStreak >= $threshold) {
                return $bonus;
            }
        }

        return 0;
    }

    // ==================== LEVELING ====================

    public function determineLevel(int $xp): string
    {
        $currentLevel = StudentStateSchema::LEVEL_PEMULA;

        foreach (StudentStateSchema::LEVEL_THRESHOLDS as $level) {
            if ($xp >= $level['min']) {
                $currentLevel = $level['name'];
            }
        }

        return $currentLevel;
    }

    public function getLevelProgress(int $xp): array
    {
        $levels       = StudentStateSchema::LEVEL_THRESHOLDS;
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
            'percentage'    => (int) round(($progressXp / $totalXpNeeded) * 100),
            'xp_needed'     => $nextMin - $xp,
        ];
    }
}
