<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProgressJumpRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $variant = 'acceleration';

    public function getRuleId(): string
    {
        return 'RULE_13';
    }

    public function getRuleName(): string
    {
        return 'Accelerated Difficulty Jump';
    }

    public function getPriority(): int
    {
        return 40;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_ACCELERATED_JUMP;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->isBeginnerDifficulty($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyAcceleratedJump($state);
    }
}
