<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleFinalProjectVisualPersistentFail extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_01';

    protected string $ruleName = 'Final Project Visual Persistent Fail';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION;

    protected int $priority = 3;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->isVisualLearner($facts)
            && $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFinalProjectVisualPersistent($state);
    }
}
