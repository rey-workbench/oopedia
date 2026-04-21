<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProgressMasteryMedRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $variant = 'result';

    public function getRuleId(): string
    {
        return 'RULE_15';
    }

    public function getRuleName(): string
    {
        return 'Mastery At Medium Difficulty';
    }

    public function getPriority(): int
    {
        return 42;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_MASTERY_MEDIUM;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->isMediumDifficulty($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyMasteryMedium($state);
    }
}
