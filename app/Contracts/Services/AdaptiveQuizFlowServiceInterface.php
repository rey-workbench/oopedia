<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\Question;

interface AdaptiveQuizFlowServiceInterface
{
    public function processAdaptiveAttempt(Material $material, Question $question, string $userId, array $data): array;
}
