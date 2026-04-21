<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverReviewPrevRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_23';
    }

    public function getRuleName(): string
    {
        return 'Review Previous Material';
    }

    public function getPriority(): int
    {
        return 36;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_REVIEW_PREVIOUS;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && $this->isBeginnerDifficulty($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyReviewPreviousMaterial($state);
    }
}
