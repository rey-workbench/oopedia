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
        return $this->hasFact($facts, AdaptiveConstants::FACT_SCORE_CRITICAL)
            && $this->hasAnyFact($facts, [AdaptiveConstants::FACT_DIFF_MEDIUM, AdaptiveConstants::FACT_DIFF_HARD])
            && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT); // Final Project has its own rules
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyCriticalBacktracking($state);
    }
}
