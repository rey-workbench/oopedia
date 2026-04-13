<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RulePersistentVisualSafetyNet extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_14';

    protected string $ruleName = 'Persistent Visual Safety Net';

    protected string $actionCode = AdaptiveConstants::ACTION_PERSISTENT_VISUAL_NET;

    protected int $priority = 5;

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->isVisualLearner($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyPersistentVisualSafety(
            $state,
            'Anda mengalami kesulitan signifikan. Mari kita ulas materi secara menyeluruh.',
        );
    }
}
