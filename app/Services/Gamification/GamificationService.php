<?php

namespace App\Services\Gamification;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\StudentLevel;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;

class GamificationService implements GamificationServiceInterface
{
    public function __construct(
        private readonly StudentStateRepositoryInterface $studentStateRepo,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function determineLevel(int $xp): StudentLevel
    {
        foreach (array_reverse(AC::LEVEL_THRESHOLDS) as $level) {
            if ($xp >= $level['min']) {
                return StudentLevel::tryFrom($level['name']) ?? StudentLevel::PEMULA;
            }
        }

        return StudentLevel::PEMULA;
    }

    public function addXp(string $userId, int $amount): StudentState
    {
        $state = $this->getStudentState($userId);
        $newXp = $state->xp + $amount;

        return $this->studentStateRepo->update($userId, [
            'xp'    => $newXp,
            'level' => $this->determineLevel($newXp)->value,
        ]);
    }

    public function incrementStreak(string $userId): StudentState
    {
        $state     = $this->getStudentState($userId);
        $newStreak = $state->streak + 1;

        return $this->studentStateRepo->update($userId, [
            'streak'     => $newStreak,
            'max_streak' => max($state->max_streak, $newStreak),
        ]);
    }

    public function resetStreak(string $userId): StudentState
    {
        return $this->studentStateRepo->update($userId, ['streak' => 0]);
    }

    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        QuestionDifficulty $difficulty = QuestionDifficulty::BEGINNER,
        int $timeSpent = 0,
    ): array {
        $xpReward = match ($difficulty) {
            QuestionDifficulty::HARD   => AC::XP_REWARD_HARD,
            QuestionDifficulty::MEDIUM => AC::XP_REWARD_MEDIUM,
            default                    => AC::XP_REWARD_BEGINNER,
        };

        if ($usedHint) {
            $xpReward = max(0, $xpReward - AC::XP_PENALTY_HINT);
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
        foreach (AC::STREAK_XP_BONUSES as $threshold => $bonus) {
            if ($currentStreak >= $threshold) {
                return $bonus;
            }
        }

        return 0;
    }

    public function checkStreakBonus(array $state): ?array
    {
        $streak = $state['streak'] ?? 0;
        $bonus  = $this->calculateStreakBonusXP($streak);

        if ($bonus > 0) {
            return [
                'bonus_xp' => $bonus,
                'message'  => "Streak {$streak}! Bonus {$bonus} XP.",
            ];
        }

        return null;
    }

    public function applySubmissionRewards(
        string $userId,
        bool $isCorrect,
        QuestionDifficulty $difficulty,
        int $timeSpent,
        bool $usedHint,
    ): array {
        $state      = $this->getStudentState($userId);
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

        // Update performance metrics (all flat columns)
        $newCorrect = $state->correct_count + ($isCorrect ? 1 : 0);
        $newWrong   = $state->wrong_count   + ($isCorrect ? 0 : 1);

        $this->studentStateRepo->update($userId, [
            'total_answered' => $state->total_answered + 1,
            'correct_count'  => $newCorrect,
            'wrong_count'    => $newWrong,
            'wrong_streak'   => $isCorrect ? 0 : $state->wrong_streak + 1,
            'hints_used'     => $state->hints_used                    + ($usedHint ? 1 : 0),
            'last_active_at' => now(),
        ]);

        return [
            'xp_reward'  => $xpReward,
            'is_correct' => $isCorrect,
            'new_xp'     => $updatedState->xp,
            'new_level'  => $updatedState->level,
            'streak'     => $updatedState->streak,
            'message'    => $rewardData['message'],
        ];
    }
}
