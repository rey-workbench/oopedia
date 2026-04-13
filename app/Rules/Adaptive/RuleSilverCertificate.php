<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesAchievement;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleSilverCertificate extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $ruleId = 'RULE_10';

    protected string $ruleName = 'Silver Certificate';

    protected string $actionCode = AdaptiveConstants::ACTION_SILVER_CERTIFICATE;

    protected int $priority = 22;

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)
            && $this->isFinalProject($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applySilverCertificate($state, $context);
    }
}
