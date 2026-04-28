<?php

declare(strict_types=1);

namespace App\Services\Adaptive\Handlers;

use App\Enums\Lms\QuestionDifficulty;
use App\Services\Adaptive\Handlers\Facts\PrimaryFactProcessorInterface;

final class PrimaryFactHandler
{
    /** @var PrimaryFactProcessorInterface[] */
    private array $processors;

    public function __construct()
    {
        $this->processors = [
            new Facts\Primary\AccuracyProcessor,
            new Facts\Primary\EfficiencyProcessor,
            new Facts\Primary\BehaviourProcessor,
            new Facts\Primary\DifficultyProcessor,
        ];
    }

    /**
     * Map raw quiz performance data into primary facts (G-codes).
     */
    public function gather(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        QuestionDifficulty $difficulty,
    ): array {
        $facts = [];

        foreach ($this->processors as $processor) {
            $fact = $processor->process($isCorrect, $usedHint, $timeSpent, $difficulty);
            if ($fact) {
                $facts[] = $fact;
            }
        }

        return array_values(array_unique($facts));
    }
}
