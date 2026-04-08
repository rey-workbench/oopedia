<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleVisualProjectRevision extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_12';

    protected string $ruleName = 'Visual Project Revision';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;

    protected int $priority = 15;

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact(
            $facts,
            [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL],
        )
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualProjectRevision($state, $context['facts'] ?? []);
    }
}
