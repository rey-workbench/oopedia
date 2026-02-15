<?php

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 4: Logic Recovery
 * IF (G02 AND G10 AND G16 AND G12) THEN H04
 * 
 * Triggers when student has remedial score on medium level,
 * made logic errors, and used hints.
 */
class Rule04_LogicRecovery extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_04';
    protected string $ruleName = 'Logic Recovery';
    protected string $actionCode = 'H04';
    protected int $priority = 20; // Medium-high priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G02', 'G10', 'G16', 'G12']);
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
