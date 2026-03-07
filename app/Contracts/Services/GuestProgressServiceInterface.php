<?php

namespace App\Contracts\Services;

interface GuestProgressServiceInterface
{
    /**
     * Get the guest's progress data from session.
     */
    public function getProgress(): array;

    /**
     * Save progress for a specific question.
     */
    public function saveProgress(array $data, bool $isCorrect, string $questionId): void;

    /**
     * Reset progress for a specific material.
     */
    public function resetMaterialProgress(string $materialId): void;

    /**
     * Clear all guest progress.
     */
    public function clearAllProgress(): void;

    /**
     * Get the current XP and Streak from session.
     */
    public function getGamificationState(): array;

    /**
     * Save the new XP and Streak to session.
     */
    public function saveGamificationState(int $xp, int $streak): void;
}
