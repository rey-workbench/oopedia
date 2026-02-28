<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 11: Bronze Certificate
 * IF (G02 AND G18 AND G26) THEN H11
 *
 * Requires remedial score on final project AND satisfactory progress.
 * Satisfactory progress prevents getting a certificate too early.
 */
class Rule11_BronzeCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_11';
    protected string $ruleName = 'Bronze Certificate';
    protected string $actionCode = AdaptiveConstants::ACTION_BRONZE_CERTIFICATE;
    protected int $priority = 20; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAnyFact($facts, [AdaptiveConstants::FACT_SCORE_STANDARD, AdaptiveConstants::FACT_SCORE_MASTERY])
            && $this->hasFact($facts, AdaptiveConstants::FACT_HINT_USED)
            && $this->hasFact($facts, AdaptiveConstants::FACT_IS_FINAL_PROJECT);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Bagus! Anda layak mendapatkan Sertifikat PERUNGGU sebagai Junior Object-Oriented Programmer.';
        $state['certification'] = 'bronze';
        $state['achievement'] = 'bronze_certificate';

        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'bronze_junior';
        $state['gamification_data']['badges'] = $badges;

        return $state;
    }
}
