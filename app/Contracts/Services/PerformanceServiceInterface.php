<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function getStudentState(string $userId): StudentState;

    public function updateMetricsFromInteraction(
        string $userId,
        bool $isCorrect,
        int $timeSpent,
        QuestionDifficulty $difficulty,
        bool $usedHint,
    ): StudentState;
}
