<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesAchievement;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class AwardBronzeCertRule extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $variant = 'certificate';

    public function getRuleId(): string
    {
        return 'RULE_11';
    }

    public function getRuleName(): string
    {
        return 'Bronze Certificate Award';
    }

    public function getPriority(): int
    {
        return 23;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_BRONZE_CERTIFICATE;
    }

    public function evaluate(array $facts): bool
    {
        return $this->isFinalProject($facts)
            && $this->hasPassingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyBronzeCertificate($state, $context);
    }
}
