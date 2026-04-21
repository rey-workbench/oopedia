<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProgressMaterialRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    public function getRuleId(): string
    {
        return 'RULE_14';
    }

    public function getRuleName(): string
    {
        return 'Accelerated Material Graduation';
    }

    public function getPriority(): int
    {
        return 45;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_ACCELERATED_MATERIAL;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && $this->isHardDifficulty($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyAcceleratedMaterialPromotion($state, $context);
    }
}
