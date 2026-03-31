<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesAchievement;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_GoldCertificate extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $ruleId = 'RULE_09';

    protected string $ruleName = 'Gold Certificate';

    protected string $actionCode = AdaptiveConstants::ACTION_GOLD_CERTIFICATE;

    protected int $priority = 21;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyGoldCertificate($state, $context);
    }
}
