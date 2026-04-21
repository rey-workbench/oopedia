<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesAchievement;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class AwardGoldCertRule extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $variant = 'certificate';

    public function getRuleId(): string
    {
        return 'RULE_09';
    }

    public function getRuleName(): string
    {
        return 'Gold Certificate Award';
    }

    public function getPriority(): int
    {
        return 21;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_GOLD_CERTIFICATE;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyGoldCertificate($state, $context);
    }
}
