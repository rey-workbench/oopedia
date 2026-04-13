<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\Question;

interface AdaptiveQuizFlowServiceInterface
{
    public function processAdaptiveAttemptByIds(
        string $materialId,
        string $questionId,
        string $userId,
        array $data,
    ): array;

    public function processAdaptiveAttempt(Material $material, Question $question, string $userId, array $data): array;
}
