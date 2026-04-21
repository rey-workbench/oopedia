<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverRemedialIndepRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_22';
    }

    public function getRuleName(): string
    {
        return 'Remedial Independent Study';
    }

    public function getPriority(): int
    {
        return 34;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_REMEDIAL_INDEPENDENT;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyRemedialIndependent($state);
    }
}
