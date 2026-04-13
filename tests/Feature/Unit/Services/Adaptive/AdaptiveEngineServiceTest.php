<?php

namespace Tests\Feature\Unit\Services\Adaptive;

use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Services\Adaptive\AdaptiveEngineService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdaptiveEngineServiceTest extends TestCase
{
    public function test_it_skips_accelerated_jump_when_target_difficulty_already_reached_for_same_material(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ];

        $result = $service->evaluate(
            $facts,
            [
                'adaptive_state' => [
                    'current_material_id' => 'material-1',
                    'target_difficulty'   => AdaptiveConstants::DIFFICULTY_MEDIUM,
                ],
            ],
            [
                'material_id' => 'material-1',
                'is_correct'  => true,
            ],
        );

        $this->assertSame('RULE_20', $result['triggered_rule']['id'] ?? null);
        $this->assertSame(AdaptiveConstants::ACTION_STANDARD_PROMOTION, $result['triggered_rule']['action'] ?? null);
    }

    public function test_it_allows_accelerated_jump_after_moving_to_a_different_material(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_BEGINNER,
        ];

        $result = $service->evaluate(
            $facts,
            [
                'adaptive_state' => [
                    'current_material_id' => 'material-1',
                    'target_difficulty'   => AdaptiveConstants::DIFFICULTY_MEDIUM,
                ],
            ],
            [
                'material_id' => 'material-2',
                'is_correct'  => true,
            ],
        );

        $this->assertSame('RULE_18', $result['triggered_rule']['id'] ?? null);
        $this->assertSame(AdaptiveConstants::ACTION_ACCELERATED_JUMP, $result['triggered_rule']['action'] ?? null);
    }

    public function test_it_skips_repeated_accelerated_material_promotion_in_same_material(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_NEXT_UNLOCKED,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ];

        $result = $service->evaluate(
            $facts,
            [
                'adaptive_state' => [
                    'current_material_id' => 'material-7',
                    'last_rule'           => [
                        'action' => AdaptiveConstants::ACTION_ACCELERATED_MATERIAL_PROMOTION,
                    ],
                ],
            ],
            [
                'material_id' => 'material-7',
                'is_correct'  => true,
            ],
        );

        $this->assertNull($result['triggered_rule']);
        $this->assertSame(AdaptiveConstants::ACTION_NEXT_QUESTION, $result['new_state']['next_action'] ?? null);
    }

    public function test_rule_action_codes_are_aligned_for_project_and_safety_net_rules(): void
    {
        $service = new AdaptiveEngineService;

        $expectedActions = [
            'RULE_07' => AdaptiveConstants::ACTION_VISUAL_PROJECT_REVISION,
            'RULE_08' => AdaptiveConstants::ACTION_TEXTUAL_PROJECT_REVISION,
            'RULE_03' => AdaptiveConstants::ACTION_PERSISTENT_VISUAL_NET,
            'RULE_04' => AdaptiveConstants::ACTION_PERSISTENT_TEXTUAL_NET,
        ];

        foreach ($expectedActions as $ruleId => $expectedActionCode) {
            $rule = $service->getRuleById($ruleId);

            $this->assertNotNull($rule, "Rule {$ruleId} should exist in registry.");
            $this->assertSame($expectedActionCode, $rule->getActionCode());
        }
    }

    public function test_it_collects_all_matching_rules_while_applying_highest_priority_action(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ];

        $result = $service->evaluate(
            $facts,
            [],
            [
                'material_id' => 'material-overlap',
                'is_correct'  => true,
            ],
        );

        $this->assertSame('RULE_09', $result['triggered_rule']['id'] ?? null);

        $matchedRuleIds = array_column($result['matched_rules'] ?? [], 'id');
        $this->assertContains('RULE_09', $matchedRuleIds);
        $this->assertContains('RULE_10', $matchedRuleIds);
    }

    public function test_mastery_medium_is_prioritized_over_accelerated_material_promotion_when_both_match(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_DIFF_MEDIUM,
            AdaptiveConstants::FACT_NEXT_UNLOCKED,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ];

        $result = $service->evaluate(
            $facts,
            [],
            [
                'material_id' => 'material-priority-overlap',
                'is_correct'  => true,
            ],
        );

        $this->assertSame('RULE_16', $result['triggered_rule']['id'] ?? null);
        $this->assertSame(AdaptiveConstants::DIFFICULTY_HARD, $result['new_state']['target_difficulty'] ?? null);
    }

    public function test_gold_certificate_rule_writes_certification_into_learning_profile(): void
    {
        $service = new AdaptiveEngineService;

        $facts = [
            AdaptiveConstants::FACT_SCORE_MASTERY,
            AdaptiveConstants::FACT_TIME_FAST,
            AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
        ];

        $result = $service->evaluate(
            $facts,
            [
                'learning_profile' => [
                    'certifications' => [],
                ],
            ],
            [
                'material_id' => 'material-certificate-test',
                'is_correct'  => true,
            ],
        );

        $this->assertSame('RULE_09', $result['triggered_rule']['id'] ?? null);
        $this->assertSame(
            AdaptiveConstants::CERT_GOLD,
            $result['new_state']['learning_profile']['certifications']['material-certificate-test'] ?? null,
        );
    }

    #[DataProvider('ruleReachabilityProvider')]
    public function test_each_registered_rule_has_at_least_one_triggerable_scenario(array $facts, string $expectedRuleId): void
    {
        $service = new AdaptiveEngineService;

        $result = $service->evaluate(
            $facts,
            [],
            [
                'material_id' => 'material-reachability',
                'is_correct'  => true,
            ],
        );

        $this->assertSame($expectedRuleId, $result['triggered_rule']['id'] ?? null);
    }

    /** @return array<string, array{0: array<int, string>, 1: string}> */
    public static function ruleReachabilityProvider(): array
    {
        return [
            'RULE_03' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_PERSISTENT_FAIL,
                AdaptiveConstants::FACT_STYLE_VISUAL,
            ], 'RULE_03'],
            'RULE_04' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
                AdaptiveConstants::FACT_PERSISTENT_FAIL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
            ], 'RULE_04'],
            'RULE_05' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_STYLE_VISUAL,
                AdaptiveConstants::FACT_DIFF_BEGINNER,
            ], 'RULE_05'],
            'RULE_06' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
                AdaptiveConstants::FACT_DIFF_BEGINNER,
            ], 'RULE_06'],
            'RULE_07' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
                AdaptiveConstants::FACT_STYLE_VISUAL,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            ], 'RULE_07'],
            'RULE_08' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            ], 'RULE_08'],
            'RULE_01' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_PERSISTENT_FAIL,
                AdaptiveConstants::FACT_STYLE_VISUAL,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            ], 'RULE_01'],
            'RULE_02' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
                AdaptiveConstants::FACT_PERSISTENT_FAIL,
                AdaptiveConstants::FACT_STYLE_TEXTUAL,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
            ], 'RULE_02'],
            'RULE_12' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
                AdaptiveConstants::FACT_ERROR_SYNTAX,
                AdaptiveConstants::FACT_DIFF_MEDIUM,
                AdaptiveConstants::FACT_HINT_USED,
            ], 'RULE_12'],
            'RULE_13' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
                AdaptiveConstants::FACT_ERROR_LOGIC,
                AdaptiveConstants::FACT_DIFF_MEDIUM,
                AdaptiveConstants::FACT_HINT_USED,
            ], 'RULE_13'],
            'RULE_09' => [[
                AdaptiveConstants::FACT_SCORE_MASTERY,
                AdaptiveConstants::FACT_TIME_FAST,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
                AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
            ], 'RULE_09'],
            'RULE_10' => [[
                AdaptiveConstants::FACT_SCORE_STANDARD,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
                AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
            ], 'RULE_10'],
            'RULE_11' => [[
                AdaptiveConstants::FACT_SCORE_STANDARD,
                AdaptiveConstants::FACT_HINT_USED,
                AdaptiveConstants::FACT_IS_FINAL_PROJECT,
                AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
            ], 'RULE_11'],
            'RULE_14' => [[
                AdaptiveConstants::FACT_SCORE_CRITICAL,
                AdaptiveConstants::FACT_DIFF_MEDIUM,
            ], 'RULE_14'],
            'RULE_15' => [[
                AdaptiveConstants::FACT_SCORE_MASTERY,
                AdaptiveConstants::FACT_TIME_FAST,
                AdaptiveConstants::FACT_DIFF_HARD,
                AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
                AdaptiveConstants::FACT_IN_MODULE,
            ], 'RULE_15'],
            'RULE_16' => [[
                AdaptiveConstants::FACT_SCORE_MASTERY,
                AdaptiveConstants::FACT_TIME_FAST,
                AdaptiveConstants::FACT_DIFF_MEDIUM,
            ], 'RULE_16'],
            'RULE_17' => [[
                AdaptiveConstants::FACT_SCORE_MASTERY,
                AdaptiveConstants::FACT_TIME_FAST,
                AdaptiveConstants::FACT_DIFF_BEGINNER,
                AdaptiveConstants::FACT_NEXT_UNLOCKED,
                AdaptiveConstants::FACT_SATISFACTORY_PROGRESS,
            ], 'RULE_17'],
            'RULE_18' => [[
                AdaptiveConstants::FACT_SCORE_MASTERY,
                AdaptiveConstants::FACT_TIME_FAST,
                AdaptiveConstants::FACT_DIFF_BEGINNER,
            ], 'RULE_18'],
            'RULE_19' => [[
                AdaptiveConstants::FACT_SCORE_REMEDIAL,
            ], 'RULE_19'],
            'RULE_20' => [[
                AdaptiveConstants::FACT_SCORE_STANDARD,
                AdaptiveConstants::FACT_DIFF_BEGINNER,
            ], 'RULE_20'],
        ];
    }
}
