<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Safety;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesCrisisIntervention;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class SafetyFinalMixedPersistentRule extends BaseAdaptiveRule
{
    use AppliesCrisisIntervention;

    public function getRuleId(): string
    {
        return 'RULE_25';
    }

    public function getRuleName(): string
    {
        return 'Final Project Mixed Persistent Failure';
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_FINAL_PROJECT_MIXED_PERSISTENT;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasFailingScore($facts)
            && $this->isMixedLearner($facts)
            && $this->hasPersistentFailure($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFinalProjectMixedPersistent($state);
    }
}
