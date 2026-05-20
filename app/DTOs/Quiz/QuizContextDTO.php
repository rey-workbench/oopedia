<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;

/**
 * Data Transfer Object for Quiz Context.
 */
final readonly class QuizContextDTO
{
    public function __construct(
        public Material $material,
        public ?QuestionDifficulty $difficulty,
        public string $userId,
        public bool $isGuest,
        public array $guestProgress = [],
        public ?QuestionDifficulty $targetDifficulty = null,
    ) {
    }
}
