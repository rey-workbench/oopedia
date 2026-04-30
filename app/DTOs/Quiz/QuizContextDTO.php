<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;

/**
 * Data Transfer Object for Quiz Context.
 */
final class QuizContextDTO
{
    public function __construct(
        public readonly Material $material,
        public readonly ?QuestionDifficulty $difficulty,
        public readonly string $userId,
        public readonly bool $isGuest,
        public readonly array $guestProgress = [],
        public readonly ?QuestionDifficulty $targetDifficulty = null,
    ) {}
}
