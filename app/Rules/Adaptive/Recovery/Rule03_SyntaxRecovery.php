<?php

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 3: Syntax Recovery
 * IF (G02 AND G09 AND G14) THEN H03
 *
 * Triggers when student has remedial score on medium level,
 * made syntax errors, and used hints.
 */
class Rule03_SyntaxRecovery extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_03';
    protected string $ruleName = 'Syntax Recovery';
    protected string $actionCode = AdaptiveConstants::ACTION_SYNTAX_RECOVERY;
    protected int $priority = 20; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
            AdaptiveConstants::FACT_ERROR_SYNTAX,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_HINT_USED,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['recommendation'] = 'Latihan Sintaksis';
        $state['next_action'] = 'STUDY_SYNTAX';
        $state['message'] = 'Sepertinya Anda butuh penguatan sintaks. Mari pelajari contoh kode secara mendalam.';
        $state['recovery_type'] = 'syntax';

        return $state;
    }
}
