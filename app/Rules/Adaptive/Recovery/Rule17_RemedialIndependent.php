<?php

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 17: Remedial Independent
 * IF (G02 AND G11) THEN STUDY_MIXED
 *
 * Triggers when student has remedial score but did not use hints (independent).
 * Without hint data we cannot pinpoint syntax vs logic error, so a
 * mixed-content review is recommended to cover both angles.
 */
class Rule17_RemedialIndependent extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_17';

    protected string $ruleName = 'Remedial Independent';

    protected string $actionCode = AdaptiveConstants::ACTION_LOGIC_RECOVERY;

    protected int $priority = 48;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
            AdaptiveConstants::FACT_HINT_NONE,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Perkuat Pemahaman';
        $state['next_action']    = 'STUDY_MIXED';
        $state['message']        = 'Nilai Anda perlu sedikit perbaikan. Mari perkuat pemahaman melalui materi komprehensif sebelum melanjutkan.';
        $state['recovery_type']  = 'remedial_independent';

        return $state;
    }
}
