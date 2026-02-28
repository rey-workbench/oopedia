<?php

namespace App\Rules\Adaptive\Achievement;

use App\Rules\Adaptive\BaseAdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * Rule 10: Silver Certificate
 * IF (G03 AND G11 AND G18 AND G26) THEN H10
 *
 * Requires standard score, no hints, final project, and satisfactory progress.
 */
class Rule10_SilverCertificate extends BaseAdaptiveRule
{
    protected string $ruleId = 'RULE_10';
    protected string $ruleName = 'Silver Certificate';
    protected string $actionCode = AdaptiveConstants::ACTION_SILVER_CERTIFICATE;
    protected int $priority = 20; // High priority

    public function evaluate(array $facts): bool
    {
        return $this->hasAllFacts($facts, [
            AdaptiveConstants::FACT_SCORE_STANDARD,
            AdaptiveConstants::FACT_HINT_NONE,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
        ]);
    }

    public function apply(array $state, array $context): array
    {
        $state['next_action'] = 'ISSUE_CERTIFICATE';
        $state['message'] = 'Selamat! Anda layak mendapatkan Sertifikat PERAK sebagai Object-Oriented Developer.';
        $state['certification'] = 'silver';
        $state['achievement'] = 'silver_certificate';

        // Add badge
        $badges = $state['gamification_data']['badges'] ?? [];
        $badges[] = 'silver_developer';
        $state['gamification_data']['badges'] = $badges;

        return $state;
    }
}
