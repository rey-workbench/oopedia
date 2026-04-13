<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleVisualCrisisIntervention extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_05';

    protected string $ruleName = 'Visual Crisis Intervention';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;

    protected int $priority = 10;

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isVisualLearner($facts)
            && $this->isBeginnerDifficulty($facts)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualCrisis(
            $state,
            'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.',
        );
    }
}
