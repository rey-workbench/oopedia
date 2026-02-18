<?php

namespace App\Contracts\Services;

use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function getStudentState(int $userId): StudentState;

    public function getUserInitialLevel(int $userId, int $materialId): ?string;

    public function setUserInitialLevel(int $userId, int $materialId, string $level): void;

    public function getUserLearningStyle(int $userId, int $materialId): ?string;

    public function setUserLearningStyle(int $userId, int $materialId, string $style): void;

    public function updateLearningStyleFromInteraction(int $userId, string $questionType, int $timeSpent): string;

    public function updateStudentPerformance(int $userId, bool $isCorrect, int $timeSpent = 0, bool $usedHint = false): StudentState;

    public function calculateAverageTimeSpent(int $userId, int $materialId): float;

    public function calculateTotalTimeSpent(int $userId, int $materialId): float;

    /** @return array<string, mixed> */
    public function getKnowledgeGaps(int $userId, int $materialId): array;

    public function getWeakestTopic(int $userId, int $materialId): ?string;

    public function isFastLearner(int $userId, int $materialId, array $currentState): bool;

    public function isFatigued(int $userId, int $materialId, array $currentState): bool;

    /** @return array<int, mixed> */
    public function getCompletedMaterials(int $userId): array;

    public function markMaterialCompleted(int $userId, int $materialId): void;

    /** @return array<string, mixed> */
    public function getPersonalizationProfile(int $userId, int $materialId, array $currentState): array;

    public function calculateScore(bool $isCorrect, bool $usedHint, int $timeSpent, ?string $difficulty = 'beginner'): int;
}
