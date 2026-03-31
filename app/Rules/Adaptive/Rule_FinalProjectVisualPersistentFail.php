<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_FinalProjectVisualPersistentFail extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_18';

    protected string $ruleName = 'Final Project Visual Persistent Fail';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION;

    protected int $priority = 3; // Highest priority — supersedes regular persistent rules

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFinalProjectVisualPersistent($state);
    }
}
