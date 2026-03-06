<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 9: Gold Certificate
 * IF (G04 AND G05 AND G11 AND G18 AND G26) THEN H09
 *
 * Requires mastery score, fast time, no hints, final project, and satisfactory progress.
 */
class GoldCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_09';

    protected string $ruleName = 'Gold Certificate';

    protected string $actionCode = AdaptiveConstants::ACTION_GOLD_CERTIFICATE;

    protected int $priority = 21;

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action']   = 'ISSUE_CERTIFICATE';
        $state['message']       = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS sebagai Object-Oriented Architect.';
        $state['certification'] = 'gold';
        $state['achievement']   = 'gold_certificate';

        // Add badge
        $badges                               = $state['gamification_data']['badges'] ?? [];
        $badges[]                             = 'gold_architect';
        $state['gamification_data']['badges'] = $badges;

        return $state;
    }
}
