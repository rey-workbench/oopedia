<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleMasteryMedium extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_16';

    protected string $ruleName = 'Mastery Medium';

    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;

    protected int $priority = 35;

    public function evaluate(array $facts): bool
    {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->isMediumDifficulty($facts)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyMasteryMedium($state);
    }
}
