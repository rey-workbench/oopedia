<?php

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 4: Logic Recovery
 * IF (G02 AND G10 AND G16 AND G12) THEN H04
 *
 * Triggers when student has remedial score on medium difficulty,
 * made logic errors, and used hints.
 */
class LogicRecovery extends BaseAdaptiveRule
{
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
        $state['recommendation'] = 'Pemahaman Konsep';
        $state['next_action'] = 'STUDY_THEORY';
        $state['message'] = 'Sintaks Anda sudah baik, tapi pemahaman konsep perlu diperkuat. Mari ulas kembali teori fundamental.';
        $state['recovery_type'] = 'logic';

        return $state;
    }
}
