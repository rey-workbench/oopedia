<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleVisualProjectRevision extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_12';

    protected string $ruleName = 'Visual Project Revision';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION;

    protected int $priority = 15;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->isVisualLearner($facts)
            && $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualProjectRevision($state, $context['facts'] ?? []);
    }
}
