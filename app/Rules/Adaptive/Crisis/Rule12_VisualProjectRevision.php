<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;

/**
 * Rule 12: Visual Project Revision
 * IF (G18 AND (G01 OR G02) AND G07) THEN H01
 * 
 * Triggers when student fails final project and is a visual learner.
 */
class Rule12_VisualProjectRevision extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_12';
    protected string $ruleName = 'Visual Project Revision';
    protected string $actionCode = 'H01';
    protected int $priority = 15; // High priority (project failure)

    public function evaluate(array $facts): bool
    {
        return $this->hasFact($facts, 'G18')
            && $this->hasAnyFact($facts, ['G01', 'G02'])
            && $this->hasFact($facts, 'G07');
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Ulas Materi';
        $state['next_action'] = 'STUDY_MATERIAL';
        $state['message'] = 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental.';
        $state['intervention_type'] = 'visual_project_revision';

        return $state;
    }
}
