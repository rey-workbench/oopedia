<?php

namespace App\Services\Gamification;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\StudentLevel;
use App\Models\Question;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;

class GamificationService implements GamificationServiceInterface
{
    public function __construct(
        private readonly StudentStateRepositoryInterface $studentStateRepo,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function getLevels(): array
    {
        return [
            ['name' => StudentLevel::PEMULA->value, 'min_xp' => 0, 'max_xp' => 499],
            ['name' => StudentLevel::MENENGAH->value, 'min_xp' => 500, 'max_xp' => 999],
            ['name' => StudentLevel::MAHIR->value, 'min_xp' => 1000, 'max_xp' => 1999],
            ['name' => StudentLevel::PAKAR->value, 'min_xp' => 2000, 'max_xp' => PHP_INT_MAX],
        ];
    }

    public function determineLevel(int $xp): StudentLevel
    {
        foreach (array_reverse(StudentStateSchema::LEVEL_THRESHOLDS) as $level) {
            if ($xp >= $level['min']) {
                return StudentLevel::tryFrom($level['name']) ?? StudentLevel::PEMULA;
            }
        }

        return StudentLevel::PEMULA;
    }

    public function getLevelProgress(int $xp): array
    {
        $levels       = $this->getLevels();
        $currentLevel = $this->determineLevel($xp)->value;
        $currentIndex = 0;
        foreach ($levels as $index => $level) {
            if ($level['name'] === $currentLevel) {
                $currentIndex = $index;
                break;
            }
        }

        $nextLevel  = $levels[min($currentIndex + 1, count($levels) - 1)];
        $currentMin = $levels[$currentIndex]['min_xp'];
        $nextMin    = $nextLevel['min_xp'];

        if ($currentLevel === StudentLevel::PAKAR->value) {
            return [
                'current_level' => $currentLevel,
                'next_level'    => 'Max Level',
                'percentage'    => 100,
                'xp_needed'     => 0,
            ];
        }

        $progressXp    = $xp      - $currentMin;
        $totalXpNeeded = $nextMin - $currentMin;

        return [
            'current_level' => $currentLevel,
            'next_level'    => $nextLevel['name'],
            'percentage'    => (int) round(($progressXp / $totalXpNeeded) * 100),
            'xp_needed'     => $nextMin - $xp,
        ];
    }

    public function addXp(string $userId, int $amount): StudentState
    {
        $state        = $this->getStudentState($userId);
        $gamification = $state->gamification_data ?? [];

        $oldXp = $gamification[StudentStateSchema::KEY_GLOBAL_XP] ?? 0;
        $newXp = $oldXp + $amount;

        $gamification[StudentStateSchema::KEY_GLOBAL_XP] = $newXp;

        // Recalculate level
        $newLevel = $this->determineLevel($newXp);
        $gamification[StudentStateSchema::KEY_CURRENT_LEVEL] = $newLevel->value;

        return $this->studentStateRepo->update($userId, [
            'gamification_data' => $gamification,
        ]);
    }

    public function incrementStreak(string $userId): StudentState
    {
        $state        = $this->getStudentState($userId);
        $gamification = $state->gamification_data ?? [];

        $currentStreak = ($gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0) + 1;
        $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = $currentStreak;
        $gamification[StudentStateSchema::KEY_MAX_STREAK]     = max(
            $gamification[StudentStateSchema::KEY_MAX_STREAK] ?? 0,
            $currentStreak,
        );

        return $this->studentStateRepo->update($userId, [
            'gamification_data' => $gamification,
        ]);
    }

    public function resetStreak(string $userId): StudentState
    {
        $state        = $this->getStudentState($userId);
        $gamification = $state->gamification_data ?? [];

        $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = 0;

        return $this->studentStateRepo->update($userId, [
            'gamification_data' => $gamification,
        ]);
    }

    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        QuestionDifficulty|string $difficulty = 'beginner',
        int $timeSpent = 0,
    ): array {
        $xpReward = match ($difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty) {
            'hard'   => StudentStateSchema::XP_REWARD_HARD,
            'medium' => StudentStateSchema::XP_REWARD_MEDIUM,
            default  => StudentStateSchema::XP_REWARD_BEGINNER,
        };

        if ($usedHint) {
            $xpReward = max(0, $xpReward - StudentStateSchema::XP_PENALTY_HINT);
        }

        return [
            'xp_reward' => $xpReward,
            'message'   => 'Jawaban benar!',
        ];
    }

    public function processWrongAnswer(array $state): array
    {
        return [
            'xp_reward' => 0,
            'message'   => 'Jawaban kurang tepat.',
        ];
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

    public function checkStreakBonus(array $state): ?array
    {
        $streak = $state['gamification_data'][StudentStateSchema::KEY_CURRENT_STREAK] ?? 0;
        $bonus  = $this->calculateStreakBonusXP($streak);

        if ($bonus > 0) {
            return [
                'bonus_xp' => $bonus,
                'message'  => "Streak {$streak}! Bonus " . $bonus . ' XP.',
            ];
        }

        return null;
    }

    public function applySubmissionRewards(
        string $userId,
        bool $isCorrect,
        QuestionDifficulty|string $difficulty,
        int $timeSpent,
        bool $usedHint,
    ): array {
        $state        = $this->getStudentState($userId);
        $gamification = $state->gamification_data ?? [];

        $rewardData = $isCorrect
            ? $this->calculateCorrectAnswerReward($state->toArray(), $usedHint, $difficulty, $timeSpent)
            : $this->processWrongAnswer($state->toArray());

        $xpReward     = $rewardData['xp_reward'];
        $updatedState = $this->addXp($userId, $xpReward);

        if ($isCorrect) {
            $updatedState = $this->incrementStreak($userId);
        } else {
            $updatedState = $this->resetStreak($userId);
        }

        // Updated gamification for UI feedback
        $updatedGamification = $updatedState->gamification_data ?? [];
        $currentStreak        = $updatedGamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0;

        // Update performance metrics
        $metrics = $state->performance_metrics ?? [];
        $metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] = ($metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0) + 1;

        if ($isCorrect) {
            $metrics[StudentStateSchema::KEY_CORRECT_COUNT] = ($metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_WRONG_STREAK]   = 0;
        } else {
            $metrics[StudentStateSchema::KEY_WRONG_COUNT]  = ($metrics[StudentStateSchema::KEY_WRONG_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_WRONG_STREAK] = ($metrics[StudentStateSchema::KEY_WRONG_STREAK] ?? 0) + 1;
        }

        $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] = ($metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0) + ($usedHint ? 1 : 0);

        // Save performance metrics
        $this->studentStateRepo->update($userId, [
            'performance_metrics' => $metrics,
            'last_active_at'      => now(),
        ]);

        return [
            'xp_reward'  => $xpReward,
            'is_correct' => $isCorrect,
            'new_xp'     => $updatedGamification[StudentStateSchema::KEY_GLOBAL_XP] ?? 0,
            'new_level'  => $updatedGamification[StudentStateSchema::KEY_CURRENT_LEVEL] ?? StudentLevel::PEMULA->value,
            'streak'     => $currentStreak,
            'message'    => $rewardData['message'],
        ];
    }
}
