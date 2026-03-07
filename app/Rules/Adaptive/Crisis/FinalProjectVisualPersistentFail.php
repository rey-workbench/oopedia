<?php

namespace App\Rules\Adaptive\Crisis;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 18: Final Project Visual Persistent Fail
 * IF (G22 AND G07 AND G18) THEN H12
 *
 * Triggers when visual learner fails persistently (≥3x) during Final Project.
 * Redirects to visual project revision content.
 */
class FinalProjectVisualPersistentFail extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_18';

    protected string $ruleName = 'Final Project Visual Persistent Fail';

    protected string $actionCode = AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION;

    protected int $priority = 3; // Highest priority — supersedes regular persistent rules

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_PERSISTENT_FAIL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_VISUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Revisi Proyek - Bantuan Visual';
        $state['next_action'] = 'STUDY_VISUAL';
        $state['message'] = 'Anda mengalami kesulitan berulang di Proyek Akhir. Mari ulas kembali materi secara visual sebelum mencoba lagi.';
        $state['intervention_type'] = 'final_project_visual_persistent';
        $state['force_material_review'] = true;

        return $state;
    }
}
