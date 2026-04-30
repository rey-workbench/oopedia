<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

use App\Enums\Lms\QuestionDifficulty;

/**
 * Data Transfer Object representing the outcome of a student-question interaction.
 */
final class InteractionDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $questionId,
        public readonly bool $isCorrect,
        public readonly int $timeSpent,
        public readonly QuestionDifficulty $difficulty,
        public readonly bool $usedHint,
        public readonly int $score = 0,
    ) {}
}
