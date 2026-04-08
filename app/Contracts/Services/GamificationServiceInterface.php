<?php

namespace App\Contracts\Services;

interface GamificationServiceInterface
{
    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        string $difficulty = 'beginner',
        int $timeSpent = 0,
    ): array;

    public function processWrongAnswer(array $state): array;

    public function useHint(array $state): array;

    public function calculateAccuracy(array $state): float;

    public function updateCorrectStreak(array $state): array;

    public function updateWrongStreak(array $state): array;

    public function checkStreakBonus(array $state): ?array;

    public function calculateStreakBonusXP(int $currentStreak): int;

    public function determineLevel(int $xp): string;

    public function getLevelProgress(int $xp): array;
}
