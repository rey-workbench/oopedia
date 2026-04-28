<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers\Actions;

use App\Enums\Lms\QuestionDifficulty;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class DifficultyProcessor implements ActionProcessorInterface
{
    private const ORDER = [
        QuestionDifficulty::BEGINNER->value,
        QuestionDifficulty::MEDIUM->value,
        QuestionDifficulty::HARD->value,
    ];

    public function process(array $instructions, array $state, array $context): array
    {
        $target = $instructions[StudentStateSchema::TARGET_DIFFICULTY] ?? null;

        if (! $target) {
            return $state;
        }

        $current      = $state[StudentStateSchema::TARGET_DIFFICULTY] ?? QuestionDifficulty::BEGINNER->value;
        $currentIndex = array_search($current, self::ORDER, true);

        if ($target === 'next') {
            $nextIndex                                    = min($currentIndex + 1, count(self::ORDER) - 1);
            $state[StudentStateSchema::TARGET_DIFFICULTY] = self::ORDER[$nextIndex];
        } elseif ($target === 'prev') {
            $prevIndex                                    = max($currentIndex - 1, 0);
            $state[StudentStateSchema::TARGET_DIFFICULTY] = self::ORDER[$prevIndex];
        } else {
            // Direct set if it's a valid difficulty value
            $state[StudentStateSchema::TARGET_DIFFICULTY] = $target;
        }

        return $state;
    }
}
