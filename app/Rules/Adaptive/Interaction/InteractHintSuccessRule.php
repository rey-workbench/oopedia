<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Interaction;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class InteractHintSuccessRule extends BaseAdaptiveRule
{
    public function getRuleId(): string
    {
        return 'RULE_24';
    }

    public function getRuleName(): string
    {
        return 'Hint Used With Good Score';
    }

    public function getPriority(): int
    {
        return 52;
    }

    public function getActionCode(): string
    {
        return AdaptiveConstants::ACTION_STANDARD_PROMOTION;
    }

    public function evaluate(array $facts): bool
    {
        return $this->hasPassingScore($facts)
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && ! $this->isFinalProject($facts);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_NEXT_QUESTION;
        $state['message']     = 'Jawaban benar! Namun, menggunakan hint mengurangi skor mastery Anda. Coba latihan berikutnya tanpa hint untuk mendapat skor lebih tinggi!';

        return $state;
    }
}
