<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverLogicRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_19';
    }

    public function getRuleName(): string
    {
        return 'Logic Error Recovery';
    }

    public function getPriority(): int
    {
        return 33;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_LOGIC_RECOVERY;
    }

    public function evaluate(array $facts): bool
    {
        // Widened to include HARD difficulty
        return $this->hasFailingScore($facts)
            && $this->hasLogicError($facts)
            && ($this->isMediumDifficulty($facts) || $this->isHardDifficulty($facts))
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applyLogicRecovery(
            $state,
            'Pemahaman logika Anda perlu diperkuat. Mari ulas kembali konsep dasar topik ini.',
        );
    }
}
