<?php

namespace App\Contracts\Services;

interface GamificationServiceInterface
{
    // ==================== Reward (ex-QuizRewardService) ====================

    /** @return array<string, mixed> */
    public function calculateCorrectAnswerReward(
        array $state,
        bool $usedHint = false,
        string $difficulty = 'beginner',
        int $timeSpent = 0,
    ): array;

    /** @return array<string, mixed> */
    public function processWrongAnswer(array $state): array;

    /** @return array<string, mixed> */
    public function useHint(array $state): array;

    public function calculateAccuracy(array $state): float;

    // ==================== Streak (ex-StreakService) ====================

    /** @return array<string, mixed> */
    public function updateCorrectStreak(array $state): array;

    /** @return array<string, mixed> */
    public function updateWrongStreak(array $state): array;

    /** @return array<string, mixed>|null */
    public function checkStreakBonus(array $state): ?array;

    public function calculateStreakBonusXP(int $currentStreak): int;

    // ==================== Leveling (ex-LevelingService) ====================

    public function determineLevel(int $xp): string;

    /** @return array<string, mixed> */
    public function getLevelProgress(int $xp): array;
}
