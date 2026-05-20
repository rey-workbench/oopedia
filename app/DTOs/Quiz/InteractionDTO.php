<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

use App\Enums\Lms\QuestionDifficulty;

/**
 * Data Transfer Object representing the outcome of a student-question interaction.
 */
final readonly class InteractionDTO
{
    public function __construct(
        public string $userId,
        public string $questionId,
        public bool $isCorrect,
        public int $timeSpent,
        public QuestionDifficulty $difficulty,
        public bool $usedHint,
        public int $score = 0,
    ) {
    }
}
