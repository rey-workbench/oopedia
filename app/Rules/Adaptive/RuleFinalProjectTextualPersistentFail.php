<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleFinalProjectTextualPersistentFail extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_19';

    protected string $ruleName = 'Final Project Textual Persistent Fail';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_PROJECT_REVISION;

    protected int $priority = 3;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->isTextualLearner($facts)
            && $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFinalProjectTextualPersistent($state);
    }
}
