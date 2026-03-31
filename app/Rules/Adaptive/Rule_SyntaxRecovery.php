<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesRecovery;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class Rule_SyntaxRecovery extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $ruleId = 'RULE_03';

    protected string $ruleName = 'Syntax Recovery';

    protected string $actionCode = AdaptiveConstants::ACTION_SYNTAX_RECOVERY;

    protected int $priority = 24;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_REMEDIAL,
            AdaptiveConstants::FACT_ERROR_SYNTAX,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_HINT_USED,
        ]) && $this->notHasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applySyntaxRecovery(
            $state,
            'Sepertinya Anda butuh penguatan sintaks. Mari pelajari contoh kode secara mendalam.',
        );
    }
}
