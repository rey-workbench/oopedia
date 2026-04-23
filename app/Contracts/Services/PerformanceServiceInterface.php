<?php

namespace App\Contracts\Services;

use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\LearningStyle;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function getStudentState(string $userId): StudentState;

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory $questionType, int $timeSpent): LearningStyle;

    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState;

    public function calculateAverageTimeSpent(string $userId, string $materialId): float;

    public function calculateTotalTimeSpent(string $userId, string $materialId): float;

    public function calculateScore(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        QuestionDifficulty $difficulty,
    ): int;

    public function resetMaterialMetrics(string $userId): StudentState;
}
