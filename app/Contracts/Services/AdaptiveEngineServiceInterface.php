<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface AdaptiveEngineServiceInterface
{
    /**
     * Evaluate the student state and return the recommended action.
     *
     * @param array $state Current student state
     * @return array [recommendation, actions, diagnostic_data]
     */
    public function evaluate(array $state): array;
}
