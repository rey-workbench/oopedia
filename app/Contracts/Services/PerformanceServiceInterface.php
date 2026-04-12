<?php

namespace App\Contracts\Services;

use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function getStudentState(string $userId): StudentState;

    public function getUserInitialLevel(string $userId, string $materialId): ?string;

    public function setUserInitialLevel(string $userId, string $materialId, string $level): void;

    public function getUserLearningStyle(string $userId, string $materialId): ?string;

    public function setUserLearningStyle(string $userId, string $materialId, string $style): void;

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory|string $questionType, int $timeSpent): string;

    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState;

    public function calculateAverageTimeSpent(string $userId, string $materialId): float;

    public function calculateTotalTimeSpent(string $userId, string $materialId): float;

    public function getKnowledgeGaps(string $userId, string $materialId): array;

    public function getWeakestTopic(string $userId, string $materialId): ?string;

    public function isFastLearner(string $userId, string $materialId, array $currentState): bool;

    public function isFatigued(string $userId, string $materialId, array $currentState): bool;

    public function getCompletedMaterials(string $userId): array;

    public function markMaterialCompleted(string $userId, string $materialId): void;

    public function getPersonalizationProfile(string $userId, string $materialId, array $currentState): array;

    public function calculateScore(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        QuestionDifficulty|string|null $difficulty = 'beginner',
    ): int;
}
