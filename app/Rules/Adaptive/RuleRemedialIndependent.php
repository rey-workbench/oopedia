<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesRecovery;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleRemedialIndependent extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $ruleId = 'RULE_17';

    protected string $ruleName = 'Remedial Independent';

    protected string $actionCode = AdaptiveConstants::ACTION_LOGIC_RECOVERY;

    protected int $priority = 48;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
        ])  && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyRemedialIndependent($state);
    }
}
