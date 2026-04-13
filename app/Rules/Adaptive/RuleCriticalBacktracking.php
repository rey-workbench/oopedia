<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleCriticalBacktracking extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_07';

    protected string $ruleName = 'Critical Backtracking';

    protected string $actionCode = AdaptiveConstants::ACTION_CRITICAL_BACKTRACKING;

    protected int $priority = 27;

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && ($this->isMediumDifficulty($facts) || $this->isHardDifficulty($facts))
            && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && ! $this->isFinalProject($facts); // Final Project has its own rules
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyCriticalBacktracking($state);
    }
}
