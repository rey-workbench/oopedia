<?php

namespace App\Contracts\Services;

use App\Models\StudentState;

interface FactGatheringServiceInterface
{
    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        string $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
    ): array;
}
