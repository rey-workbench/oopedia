<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverBacktrackRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $variant = 'backtrack';

    public function getRuleId(): string
    {
        return 'RULE_17';
    }

    public function getRuleName(): string
    {
        return 'Critical Performance Backtracking';
    }

    public function getPriority(): int
    {
        return 35;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_CRITICAL_BACKTRACKING;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasCriticalScore($facts)
            && ($this->isMediumDifficulty($facts) || $this->isHardDifficulty($facts))
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyCriticalBacktracking($state);
    }
}
