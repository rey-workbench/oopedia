<?php

namespace App\Contracts\Services;

use App\Enums\Lms\ContentCategory;
use App\Models\StudentState;
use App\Enums\Lms\QuestionDifficulty;

interface PerformanceServiceInterface
{
    public function getStudentState(string $userId): StudentState;

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory|string $questionType, int $timeSpent): string;

    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState;

    public function useHint(string $userId, int $count = 1): StudentState;

    public function calculateAverageTimeSpent(string $userId, string $materialId): float;

    public function calculateTotalTimeSpent(string $userId, string $materialId): float;

    public function resetMaterialMetrics(string $userId, array $adaptiveState): StudentState;
}
