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
        return $this->hasMasteryScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_TIME_FAST)
            && $this->isHardDifficulty($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_SATISFACTORY_PROGRESS)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IN_MODULE)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyModuleGraduation($state, $context);
    }
}
