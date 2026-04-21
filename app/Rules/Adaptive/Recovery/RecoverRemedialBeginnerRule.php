<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverRemedialBeginnerRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_21';
    }

    public function getRuleName(): string
    {
        return 'Remedial Redirect At Beginner';
    }

    public function getPriority(): int
    {
        return 31;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_REMEDIAL_AT_BEGINNER;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->isBeginnerDifficulty($facts)
            && ! $this->hasCriticalScore($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyRemedialBeginner($state);
    }
}
