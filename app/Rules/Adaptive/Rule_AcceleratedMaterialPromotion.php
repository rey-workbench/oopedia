<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_AcceleratedMaterialPromotion extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_20';

    protected string $ruleName = 'Accelerated Material Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION;

    protected int $priority = 35; // Higher priority than RULE_06 (40)

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_NEXT_UNLOCKED,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyAcceleratedMaterialPromotion($state);
    }
}
