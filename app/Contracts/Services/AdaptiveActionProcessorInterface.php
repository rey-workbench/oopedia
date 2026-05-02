<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\StudentState;

interface AdaptiveActionProcessorInterface
{
    /**
     * Process a list of adaptive recommendations and apply changes to the student state.
     *
     * @param bool $isCorrect Whether the current answer was correct
     */
    public function process(StudentState $studentState, array $actions, string $materialId, bool $isCorrect): StudentState;
}
