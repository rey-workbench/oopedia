<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleTextualProjectRevision extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_13';

    protected string $ruleName = 'Textual Project Revision';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_PROJECT_REVISION;

    protected int $priority = 15;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->isTextualLearner($facts)
            && $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyTextualProjectRevision($state, $context['facts'] ?? []);
    }
}
