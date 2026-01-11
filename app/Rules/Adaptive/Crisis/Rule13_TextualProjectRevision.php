<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 13: Textual Project Revision
 * IF (G18 AND (G01 OR G02) AND G08) THEN H02
 * 
 * Triggers when student fails final project and is a textual learner.
 */
class Rule13_TextualProjectRevision extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_13';
    protected string $ruleName = 'Textual Project Revision';
    protected string $actionCode = 'H02';
    protected int $priority = 15; // High priority (project failure)
    
    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, 'G18')
            && $this->hasAnyFact($facts, ['G01', 'G02'])
            && $this->hasFact($facts, 'G08');
    }
    
    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Materi Tekstual';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep dengan Dokumentasi Detail.';
        $state['intervention_type'] = 'textual_project_revision';
        
        return $state;
    }
}
