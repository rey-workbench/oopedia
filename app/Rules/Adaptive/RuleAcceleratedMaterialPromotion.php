<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesProgression;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleAcceleratedMaterialPromotion extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $ruleId = 'RULE_17';

    protected string $ruleName = 'Accelerated Material Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION;

    protected int $priority = 36;

    public function evaluate(array $facts): bool
    {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->hasFact($facts, AdaptiveConstants::FACT_NEXT_UNLOCKED)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyAcceleratedMaterialPromotion($state, $context);
    }
}
