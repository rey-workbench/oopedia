<?php

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\StudentLevel;
use App\Models\StudentState;

interface GamificationServiceInterface
{
    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        QuestionDifficulty|string $difficulty = 'beginner',
        int $timeSpent = 0,
    ): array;

    public function processWrongAnswer(array $state): array;

    public function checkStreakBonus(array $state): ?array;

    public function calculateStreakBonusXP(int $currentStreak): int;

    public function determineLevel(int $xp): StudentLevel;

    public function getLevelProgress(int $xp): array;

    public function addXp(string $userId, int $amount): StudentState;

    public function incrementStreak(string $userId): StudentState;

    public function resetStreak(string $userId): StudentState;

    public function applySubmissionRewards(
        string $userId,
        bool $isCorrect,
        QuestionDifficulty|string $difficulty,
        int $timeSpent,
        bool $usedHint,
    ): array;
}
