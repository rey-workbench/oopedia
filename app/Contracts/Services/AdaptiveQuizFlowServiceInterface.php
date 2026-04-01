<?php

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\Question;

interface AdaptiveQuizFlowServiceInterface
{
    /**
     * Process an adaptive quiz attempt.
     *
     * @return array<string, mixed>
     */
    public function processAdaptiveAttempt(Material $material, Question $question, string $userId, array $data): array;
}
