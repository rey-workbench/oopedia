<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyFinalVisualPersistentRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_01';
    }

    public function getRuleName(): string
    {
        return 'Final Project Visual Persistent Failure';
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_FINAL_PROJECT_VISUAL_PERSISTENT;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasFailingScore($facts)
            && $this->isVisualLearner($facts)
            && $this->hasPersistentFailure($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFinalProjectVisualPersistent($state);
    }
}
