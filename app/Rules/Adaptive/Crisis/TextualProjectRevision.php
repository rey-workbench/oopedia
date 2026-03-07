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
class TextualProjectRevision extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_13';

    protected string $ruleName = 'Textual Project Revision';

    protected string $actionCode = AdaptiveConstants::ACTION_TEXTUAL_CRISIS_INTERVENTION;

    protected int $priority = 15; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_CRITICAL, AdaptiveConstants::FACT_SCORE_REMEDIAL])
            && $this->hasFact($facts, AdaptiveConstants::FACT_STYLE_TEXTUAL)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $facts = $context['facts'] ?? [];
        $state['recommendation'] = 'Revisi Proyek - Ulas Materi';
        $state['next_action'] = 'STUDY_TEXTUAL';
        $state['intervention_type'] = 'textual_project_revision';

        if (in_array(AdaptiveConstants::FACT_ERROR_SYNTAX, $facts)) {
            $state['message'] = 'Ada kesalahan penulisan kode (sintaks) pada proyek Anda. Mari baca kembali dokumentasi teknis.';
        }
        elseif (in_array(AdaptiveConstants::FACT_ERROR_LOGIC, $facts)) {
            $state['message'] = 'Logika pemrograman Anda perlu dipertajam. Silakan ulas penjelasan teks mendalam mengenai konsep ini.';
        }
        else {
            $state['message'] = 'Proyek Anda perlu perbaikan. Mari ulas kembali penjelasan teori secara mendalam.';
        }

        return $state;
    }
}
