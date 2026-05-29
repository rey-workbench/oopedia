<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Enums\Lms\QuestionDifficulty;

/**
 * Data Transfer Object for calculating student performance score.
 */
final readonly class PerformanceScoreDTO
{
    public function __construct(
        public bool $isCorrect,
        public bool $usedHint,
        public int $timeSpent,
        public QuestionDifficulty|string $difficulty,
    ) {
    }
}
