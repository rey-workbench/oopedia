<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesRecovery;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleLogicRecovery extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $ruleId = 'RULE_04';

    protected string $ruleName = 'Logic Recovery';

    protected string $actionCode = AdaptiveConstants::ACTION_LOGIC_RECOVERY;

    protected int $priority = 25;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
            AdaptiveConstants::FACT_ERROR_LOGIC,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_HINT_USED,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyLogicRecovery(
            $state,
            'Sintaks Anda sudah baik, tapi pemahaman konsep perlu diperkuat. Mari ulas kembali teori fundamental.',
        );
    }
}
