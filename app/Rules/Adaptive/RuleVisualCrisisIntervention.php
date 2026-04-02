<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleVisualCrisisIntervention extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $ruleId = 'RULE_01';

    protected string $ruleName = 'Visual Crisis Intervention';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;

    protected int $priority = 10; // Very high priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_CRITICAL,
            AdaptiveConstants::FACT_STYLE_VISUAL,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyVisualCrisis(
            $state,
            'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.',
        );
    }
}
