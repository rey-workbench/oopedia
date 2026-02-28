<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 13: Textual Project Revision
 * IF (G01 AND G08 AND G18) THEN H13
 *
 * Triggers when textual learner has CRITICAL score on final project.
 * Remedial scores go to Bronze Certificate instead.
 */
class Rule13_TextualProjectRevision extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_13';
    protected string $ruleName = 'Textual Project Revision';
    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_REMEDIATION;
    protected int $priority = 15; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_TEXTUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Ulas Materi';
        $state['next_action'] = 'STUDY_TEXTUAL';
        $state['message'] = 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental.';
        $state['intervention_type'] = 'textual_project_revision';

        return $state;
    }
}
