<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Facts;

use App\Enums\Lms\QuestionDifficulty;

interface PrimaryFactProcessorInterface
{
    public function process(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        QuestionDifficulty $difficulty,
    ): ?string;
}
