<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesRecovery;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleLogicRecovery extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $ruleId = 'RULE_13';

    protected string $ruleName = 'Logic Recovery';

    protected string $actionCode = AdaptiveConstants::ACTION_LOGIC_RECOVERY;

    protected int $priority = 25;

    public function evaluate(array $facts): bool
    {
        return $this->hasRemedialScore($facts)
            && $this->hasLogicError($facts)
            && $this->isMediumDifficulty($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyLogicRecovery(
            $state,
            'Sintaks Anda sudah baik, tapi pemahaman konsep perlu diperkuat. Mari ulas kembali teori fundamental.',
        );
    }
}
