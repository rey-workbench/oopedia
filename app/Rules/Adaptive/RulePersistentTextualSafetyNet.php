<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RulePersistentTextualSafetyNet extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_15';

    protected string $ruleName = 'Persistent Textual Safety Net';

    protected string $actionCode = AdaptiveConstants::ACTION_PERSISTENT_TEXTUAL_NET;

    protected int $priority = 5;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->isTextualLearner($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyPersistentTextualSafety(
            $state,
            'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.',
        );
    }
}
