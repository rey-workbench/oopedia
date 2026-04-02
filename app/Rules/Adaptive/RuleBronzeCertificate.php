<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesAchievement;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleBronzeCertificate extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $ruleId = 'RULE_11';

    protected string $ruleName = 'Bronze Certificate';

    protected string $actionCode = AdaptiveConstants::ACTION_BRONZE_CERTIFICATE;

    protected int $priority = 23;

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact(
            $facts,
            [AdaptiveConstants::FACT_SCORE_STANDARD, AdaptiveConstants::FACT_SCORE_MASTERY],
        )
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyBronzeCertificate($state, $context);
    }
}
