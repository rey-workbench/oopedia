<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 15: Persistent Textual Safety Net
 * IF ((G01 OR G02) AND G22 AND G08) THEN H02
 * 
 * Triggers when student has failed ≥3 times and is a textual learner.
 * This is a safety net to prevent students from getting stuck.
 */
class Rule15_PersistentTextualSafetyNet extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_15';
    protected string $ruleName = 'Persistent Textual Safety Net';
    protected string $actionCode = 'H02';
    protected int $priority = 5; // Highest priority (persistent failure)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, ['G01', 'G02'])
            && $this->hasFact($facts, 'G22')
            && $this->hasFact($facts, 'G08');
    }
    
    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Bantuan Tekstual Intensif';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Anda mengalami kesulitan pada materi ini. Mari kita ulas dengan Penjelasan Tertulis yang detail.';
        $state['intervention_type'] = 'persistent_textual_safety';
        $state['force_material_review'] = true;
        
        return $state;
    }
}
