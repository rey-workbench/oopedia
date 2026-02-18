<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 12: Visual Project Revision
 * IF (G01 AND G07 AND G18) THEN H12
 *
 * Triggers when visual learner has CRITICAL score on final project.
 * Remedial scores go to Bronze Certificate instead.
 */
class Rule12_VisualProjectRevision extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_12';
    protected string $ruleName = 'Visual Project Revision';
    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_CRISIS_INTERVENTION;
    protected int $priority = 15; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Ulas Materi';
        $state['next_action'] = 'STUDY_VISUAL';
        $state['message'] = 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental.';
        $state['intervention_type'] = 'visual_project_revision';

        return $state;
    }
}
