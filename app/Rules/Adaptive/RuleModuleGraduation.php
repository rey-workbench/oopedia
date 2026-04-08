<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesAchievement;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleModuleGraduation extends BaseAdaptiveRule
{
    use AppliesAchievement;

    protected string $ruleId = 'RULE_08';

    protected string $ruleName = 'Module Graduation';

    protected string $actionCode = AdaptiveConstants::ACTION_MODULE_GRADUATION;

    protected int $priority = 30;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_HARD,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
            AdaptiveConstants::FACT_IN_MODULE,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyModuleGraduation($state, $context);
    }
}
