<?php

namespace App\Rules\Adaptive;

use App\Rules\Adaptive\Concerns\AppliesRecovery;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

class RuleSyntaxRecovery extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $ruleId = 'RULE_03';

    protected string $ruleName = 'Syntax Recovery';

    protected string $actionCode = AdaptiveConstants::ACTION_SYNTAX_RECOVERY;

    protected int $priority = 24;

    public function evaluate(array $facts): bool
    {
        return $this->hasRemedialScore($facts)
            && $this->hasSyntaxError($facts)
            && $this->isMediumDifficulty($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applySyntaxRecovery(
            $state,
            'Sepertinya Anda butuh penguatan sintaks. Mari pelajari contoh kode secara mendalam.',
        );
    }
}
