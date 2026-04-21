<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesProgression;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class ProgressGraduationRule extends BaseAdaptiveRule
{
    use AppliesProgression;

    protected string $variant = 'certificate';

    public function getRuleId(): string
    {
        return 'RULE_16';
    }

    public function getRuleName(): string
    {
        return 'Standard Module Graduation';
    }

    public function getPriority(): int
    {
        return 48;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_MODULE_GRADUATION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)
            && $this->isHardDifficulty($facts)
            && ! $this->isFinalProject($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyModuleGraduation($state, $context);
    }
}
