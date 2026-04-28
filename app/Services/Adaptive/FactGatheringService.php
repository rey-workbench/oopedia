<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\FactGatheringServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

final class FactGatheringService implements FactGatheringServiceInterface
{
    public function __construct(
        private readonly Handlers\PrimaryFactHandler $primaryHandler,
        private readonly Handlers\VirtualFactHandler $virtualHandler,
    ) {}

    public function gatherFacts(
        StudentState $studentState,
        bool $isCorrect,
        bool $usedHint,
        int $score,
        int $timeSpent,
        QuestionDifficulty|string $difficulty,
        string $questionId,
        string $materialId,
        ?string $moduleId = null,
    ): array {
        // 1. Gather Primary Facts (G-codes)
        $diffEnum = $difficulty instanceof QuestionDifficulty
            ? $difficulty
            : (QuestionDifficulty::tryFrom((string) $difficulty) ?? QuestionDifficulty::BEGINNER);

        $primaryFacts = $this->primaryHandler->gather($isCorrect, $usedHint, $timeSpent, $diffEnum);

        // 2. Derive Virtual Facts (V-codes)
        $virtualFacts = $this->virtualHandler->derive($primaryFacts, $studentState->adaptive_state ?? []);

        return array_values(array_unique([...$primaryFacts, ...$virtualFacts]));
    }
}
