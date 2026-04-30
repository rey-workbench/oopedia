<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Adaptive\EngineResultDTO;
use App\DTOs\Adaptive\StudentStateDTO;

interface AdaptiveEngineServiceInterface
{
    /**
     * Evaluate the student state and return the recommended action.
     *
     * @param StudentStateDTO $studentStateDTO Current student state
     * @return EngineResultDTO The evaluation results
     */
    public function evaluate(StudentStateDTO $studentStateDTO): EngineResultDTO;
}
