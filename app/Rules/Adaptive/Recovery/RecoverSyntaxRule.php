<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Concerns\AppliesRecovery;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverSyntaxRule extends BaseAdaptiveRule
{
    use AppliesRecovery;

    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_18';
    }

    public function getRuleName(): string
    {
        return 'Syntax Error Recovery';
    }

    public function getPriority(): int
    {
        return 32;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_SYNTAX_RECOVERY;
    }

    public function evaluate(array $facts): bool
    {
        // Widened to include HARD difficulty
        return $this->hasFailingScore($facts)
            && $this->hasSyntaxError($facts)
            && ($this->isMediumDifficulty($facts) || $this->isHardDifficulty($facts))
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        return $this->applySyntaxRecovery(
            $state,
            'Terjadi kesalahan sintaksis. Mari ulas kembali aturan penulisan kode pada materi ini.',
        );
    }
}
