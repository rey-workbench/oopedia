<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 1: Visual Crisis Intervention
 * IF (G01 AND G07 AND G15 AND NOT G22) THEN H01
 * 
 * Triggers when student has critical score on beginner level,
 * is a visual learner, and hasn't failed persistently yet.
 */
class Rule01_VisualCrisisIntervention extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_01';
    protected string $ruleName = 'Visual Crisis Intervention';
    protected string $actionCode = 'H01';
    protected int $priority = 10; // High priority (crisis)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, ['G01', 'G07', 'G15'])
            && $this->notHasFact($facts, 'G22');
    }
    
    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Materi Visual';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Performa Anda menurun. Mari ulas kembali materi dengan format Video/Diagram UML.';
        $state['intervention_type'] = 'visual_crisis';
        
        return $state;
    }
}
