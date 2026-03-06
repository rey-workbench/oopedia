<?php

namespace App\Rules\Adaptive\Progression;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 5: Standard Promotion
 * IF (G03 AND G11 AND (G15 OR G16 OR G17)) THEN H05
 *
 * Triggers when student passes with standard score on any difficulty, without hint.
 * Normal linear progression path.
 */
class StandardPromotion extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_05';

    protected string $ruleName = 'Standard Promotion';

    protected string $actionCode = AdaptiveConstants::ACTION_STANDARD_PROMOTION;

    protected int $priority = 50;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_STANDARD,
            AdaptiveConstants::FACT_HINT_NONE,
        ]) && $this->hasAnyFact($facts, [
            AdaptiveConstants::FACT_DIFF_BEGINNER,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_DIFF_HARD,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'NEXT_QUESTION';
        $state['message']     = $context['is_correct']
            ? 'Jawaban tepat! Mari lanjut ke soal berikutnya.'
            : 'Jawaban kurang tepat. Mari coba lagi atau ulas kembali materi jika kesulitan.';

        return $state;
    }
}
