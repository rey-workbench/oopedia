<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleTextualCrisisIntervention extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_06';

    protected string $ruleName = 'Textual Crisis Intervention';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_CRISIS_INTERVENTION;

    protected int $priority = 10;

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isTextualLearner($facts)
            && $this->isBeginnerDifficulty($facts)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyTextualCrisis(
            $state,
            'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.',
        );
    }
}
