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
        return $this->hasRemedialScore($facts)
            && $this->notHasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyRemedialIndependent($state);
    }
}
