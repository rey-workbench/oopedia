<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\StudentState;

interface AdaptiveActionProcessorInterface
{
    /**
     * Process a list of adaptive recommendations and apply changes to the student state.
     *
     * @param array $actions List of action objects {id, metadata}
     * @param string $materialId Contextual material ID
     */
    public function process(StudentState $studentState, array $actions, string $materialId): StudentState;
}
