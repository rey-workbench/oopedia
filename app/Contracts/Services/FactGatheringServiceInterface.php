<?php

namespace App\Contracts\Services;

use App\Models\StudentState;

interface FactGatheringServiceInterface
{
    /** @return array<int, string> */
    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        string $difficulty,
        int $questionId,
        int $materialId,
        ?int $moduleId = null,
    ): array;
}
