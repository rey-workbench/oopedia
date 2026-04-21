<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Recovery;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class RecoverRemedialHintRule extends BaseAdaptiveRule
{
    protected string $variant = 'intervention';

    public function getRuleId(): string
    {
        return 'RULE_29';
    }

    public function getRuleName(): string
    {
        return 'Remedial Due To High Hint Usage';
    }

    public function getPriority(): int
    {
        return 37;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_REMEDIAL_AT_BEGINNER;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasFailingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->isBeginnerDifficulty($facts)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_REMEDIAL_AT_BEGINNER;
        $state['message']     = 'Anda sering menggunakan hint namun belum berhasil menjawab dengan benar. Mari kita tinjau kembali dasar teorinya.';

        return $state;
    }
}
