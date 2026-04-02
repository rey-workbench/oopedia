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
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyMasteryMedium($state);
    }
}
