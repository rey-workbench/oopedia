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
        string $questionId,
        bool $isCorrect,
        int $timeSpent,
        QuestionDifficulty $difficulty,
        bool $usedHint,
        int $score = 0,
    ): StudentState;

    public function getStudentSessionState(string $userId): array;

    public function syncMaterialContext(string $userId, string $materialId): StudentState;

    public function calculateScore(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty|string $difficulty): int;
}
