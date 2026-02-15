<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 2: Textual Remediation
 * IF (G01 AND G08 AND G15 AND NOT G22) THEN H02
 * 
 * Triggers when student has critical score on beginner level,
 * is a textual learner, and hasn't failed persistently yet.
 */
class Rule02_TextualRemediation extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_02';
    protected string $ruleName = 'Textual Remediation';
    protected string $actionCode = 'H02';
    protected int $priority = 10; // High priority (crisis)

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G01', 'G08', 'G15'])
            && $this->notHasFact($facts, 'G22');
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Ulas Materi';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Performa Anda menurun. Mari ulas kembali materi untuk memperkuat pemahaman.';
        $state['intervention_type'] = 'textual_crisis';

        return $state;
    }
}
