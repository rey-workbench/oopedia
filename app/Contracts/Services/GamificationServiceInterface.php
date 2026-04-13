<?php

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;

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

    public function determineLevel(int $xp): string;

    public function getLevelProgress(int $xp): array;
}
