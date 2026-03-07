<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 19: Final Project Textual Persistent Fail
 * IF (G22 AND G08 AND G18) THEN H13
 *
 * Triggers when textual learner fails persistently (≥3x) during Final Project.
 * Redirects to textual project revision content.
 */
class FinalProjectTextualPersistentFail extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_19';

    protected string $ruleName = 'Final Project Textual Persistent Fail';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_PROJECT_REVISION;

    protected int $priority = 3; // Highest priority — supersedes regular persistent rules

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_TEXTUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Bantuan Tekstual';
        $state['next_action'] = 'STUDY_TEXTUAL';
        $state['message'] = 'Anda mengalami kesulitan berulang di Proyek Akhir. Mari ulas kembali materi secara mendalam sebelum mencoba lagi.';
        $state['intervention_type'] = 'final_project_textual_persistent';
        $state['force_material_review'] = true;

        return $state;
    }
}
