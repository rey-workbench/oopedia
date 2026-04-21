<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverFastWrongRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_20';
    }

    public function getRuleName(): string
    {
        return 'Careless Fast Wrong Recovery';
    }

    public function getPriority(): int
    {
        return 38;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_FAST_WRONG_RECOVERY;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyFastWrongRecovery($state);
    }
}
