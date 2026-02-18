<?php

namespace App\Contracts\Services;

interface StreakServiceInterface
{
    /** @return array<string, mixed> */
    public function updateCorrectStreak(array $state): array;

    /** @return array<string, mixed> */
    public function updateWrongStreak(array $state): array;

    /** @return array<string, mixed>|null */
    public function checkStreakBonus(array $state): ?array;

    public function calculateStreakBonusXP(int $currentStreak): int;
}
