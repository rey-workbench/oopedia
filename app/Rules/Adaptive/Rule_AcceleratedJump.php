<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_AcceleratedJump extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_06';

    protected string $ruleName = 'Accelerated Jump';

    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_JUMP;

    protected int $priority = 40;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyAcceleratedJump($state);
    }
}
