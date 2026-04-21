<?php

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

interface FactGatheringServiceInterface
{
    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        QuestionDifficulty $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
        bool $isPracticeMode = false,
    ): array;
}
