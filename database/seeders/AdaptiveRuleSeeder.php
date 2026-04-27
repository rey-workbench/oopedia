<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdaptiveRuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->cleanUp();
        $this->seedFacts();
        $this->seedActions();
        $this->seedRules();
    }

    private function cleanUp(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AdaptiveRule::truncate();
        AdaptiveFact::truncate();
        AdaptiveAction::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedFacts(): void
    {
        $facts = [
            ['code' => FactConstants::SCORE_FAIL, 'category' => 'performance', 'name' => FactConstants::NAMES[FactConstants::SCORE_FAIL]],
            ['code' => FactConstants::SCORE_PASS, 'category' => 'performance', 'name' => FactConstants::NAMES[FactConstants::SCORE_PASS]],
            ['code' => FactConstants::TIME_QUICK, 'category' => 'time', 'name' => FactConstants::NAMES[FactConstants::TIME_QUICK]],
            ['code' => FactConstants::TIME_SLOW, 'category' => 'time', 'name' => FactConstants::NAMES[FactConstants::TIME_SLOW]],
            ['code' => FactConstants::HINT_USED, 'category' => 'behaviour', 'name' => FactConstants::NAMES[FactConstants::HINT_USED]],

            // Difficulty Facts
            ['code' => FactConstants::DIFF_BEGINNER, 'category' => 'difficulty', 'name' => FactConstants::NAMES[FactConstants::DIFF_BEGINNER]],
            ['code' => FactConstants::DIFF_MEDIUM, 'category' => 'difficulty', 'name' => FactConstants::NAMES[FactConstants::DIFF_MEDIUM]],
            ['code' => FactConstants::DIFF_HARD, 'category' => 'difficulty', 'name' => FactConstants::NAMES[FactConstants::DIFF_HARD]],

            // Virtual Facts
            ['code' => FactConstants::V_EXCELLENT_RESULT, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_EXCELLENT_RESULT]],
            ['code' => FactConstants::V_STRUGGLING, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_STRUGGLING]],
            ['code' => FactConstants::V_STEADY_LEARNER, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_STEADY_LEARNER]],
            ['code' => FactConstants::V_UNFOCUSED, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_UNFOCUSED]],
            ['code' => FactConstants::V_HINT_DEPENDENT, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_HINT_DEPENDENT]],
            ['code' => FactConstants::V_CRISIS_STATE, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_CRISIS_STATE]],
        ];

        foreach ($facts as $fact) {
            AdaptiveFact::create($fact);
        }
    }

    private function seedActions(): void
    {
        $actions = [
            [
                'code' => ActionConstants::FEEDBACK,
                'variant' => 'info',
                'name' => ActionConstants::NAMES[ActionConstants::FEEDBACK],
                'description' => 'Aksi universal untuk semua jenis pesan.',
                'instructions' => [ActionConstants::KEY_FLOW => ActionConstants::FLOW_NEXT]
            ],
            [
                'code' => ActionConstants::INCREASE_DIFF,
                'variant' => 'acceleration',
                'name' => ActionConstants::NAMES[ActionConstants::INCREASE_DIFF],
                'description' => 'Meningkatkan tantangan belajar.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_UP,
                    StudentStateSchema::TARGET_DIFFICULTY => 'next'
                ]
            ],
            [
                'code' => ActionConstants::REDUCE_DIFF,
                'variant' => 'recovery',
                'name' => ActionConstants::NAMES[ActionConstants::REDUCE_DIFF],
                'description' => 'Menurunkan tantangan belajar.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_DOWN,
                    StudentStateSchema::TARGET_DIFFICULTY => 'prev'
                ]
            ],
            [
                'code' => ActionConstants::STREAK_BONUS,
                'variant' => 'gamification',
                'name' => ActionConstants::NAMES[ActionConstants::STREAK_BONUS],
                'description' => 'Pemberian XP tambahan.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NONE,
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(25)
                ]
            ],
            [
                'code' => ActionConstants::REMEDIAL,
                'variant' => 'danger',
                'name' => ActionConstants::NAMES[ActionConstants::REMEDIAL],
                'description' => 'Mengarahkan siswa kembali ke materi belajar.',
                'instructions' => [ActionConstants::KEY_FLOW => ActionConstants::FLOW_REVIEW]
            ],
        ];

        foreach ($actions as $action) {
            AdaptiveAction::create($action);
        }
    }

    private function seedRules(): void
    {
        $actionIds = AdaptiveAction::pluck('id', 'code');

        $rules = [
            // ─── DEDUCTIONS (Raw ➔ Virtual) ──────────────────────────────────
            ['rule_code' => 'D01', 'name' => 'Deduce Excellent', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::TIME_QUICK], 'deduced_facts' => [FactConstants::V_EXCELLENT_RESULT]],
            ['rule_code' => 'D02', 'name' => 'Deduce Struggle (S)', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::TIME_SLOW], 'deduced_facts' => [FactConstants::V_STRUGGLING]],
            ['rule_code' => 'D03', 'name' => 'Deduce Struggle (H)', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::HINT_USED], 'deduced_facts' => [FactConstants::V_STRUGGLING]],
            ['rule_code' => 'D04', 'name' => 'Deduce Steady', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::TIME_SLOW], 'deduced_facts' => [FactConstants::V_STEADY_LEARNER]],
            ['rule_code' => 'D05', 'name' => 'Deduce Unfocused', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::TIME_QUICK], 'deduced_facts' => [FactConstants::V_UNFOCUSED]],
            ['rule_code' => 'D06', 'name' => 'Deduce Hint Dependency', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::HINT_USED], 'deduced_facts' => [FactConstants::V_HINT_DEPENDENT]],
            ['rule_code' => 'D07', 'name' => 'Deduce Crisis', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::HINT_USED, FactConstants::TIME_SLOW], 'deduced_facts' => [FactConstants::V_CRISIS_STATE]],

            // ─── ACTIONS (Virtual + Context ➔ Core Actions) ──────────────────

            // 1. Excellent Paths
            [
                'rule_code' => 'R01',
                'name' => 'Expert Success Reward',
                'domain' => 'Mastery',
                'priority' => 1,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::DIFF_HARD],
                'action_id' => $actionIds[ActionConstants::STREAK_BONUS],
            ],
            [
                'rule_code' => 'R02',
                'name' => 'Success Promotion',
                'domain' => 'Progression',
                'priority' => 2,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT],
                'action_id' => $actionIds[ActionConstants::INCREASE_DIFF],
            ],

            // 2. Struggle Paths
            [
                'rule_code' => 'R03',
                'name' => 'Struggle Recovery',
                'domain' => 'Recovery',
                'priority' => 1,
                'required_facts' => [FactConstants::V_STRUGGLING],
                'action_id' => $actionIds[ActionConstants::REDUCE_DIFF],
            ],
            [
                'rule_code' => 'R04',
                'name' => 'Beginner Empathy',
                'domain' => 'SafetyNet',
                'priority' => 0,
                'required_facts' => [FactConstants::V_STRUGGLING, FactConstants::DIFF_BEGINNER],
                'action_id' => $actionIds[ActionConstants::FEEDBACK],
            ],

            /**
             * Helper to create/update a rule and its associated action.
             */
            // [Helper logic would be implemented here in a real scenario, but keeping the original structure as requested]

            // 3. Special Behaviors
            [
                'rule_code' => 'R05',
                'name' => 'Crisis Emergency',
                'domain' => 'SafetyNet',
                'priority' => -1,
                'required_facts' => [FactConstants::V_CRISIS_STATE],
                'action_id' => $actionIds[ActionConstants::REMEDIAL],
            ],
            [
                'rule_code' => 'R06',
                'name' => 'Unfocused Redirection',
                'domain' => 'SafetyNet',
                'priority' => 1,
                'required_facts' => [FactConstants::V_UNFOCUSED],
                'action_id' => $actionIds[ActionConstants::REMEDIAL],
            ],
            [
                'rule_code' => 'R07',
                'name' => 'Steady Navigation',
                'domain' => 'Progression',
                'priority' => 5,
                'required_facts' => [FactConstants::V_STEADY_LEARNER],
                'action_id' => $actionIds[ActionConstants::FEEDBACK],
            ],
            [
                'rule_code' => 'R08',
                'name' => 'Hint Addiction Tips',
                'domain' => 'Behaviour',
                'priority' => 10,
                'required_facts' => [FactConstants::V_HINT_DEPENDENT, FactConstants::SCORE_PASS],
                'action_id' => $actionIds[ActionConstants::FEEDBACK],
            ],

            // ─── FALLBACKS ───────────────────────────────────────────────────
            ['rule_code' => 'F01', 'name' => 'Success Fallback', 'domain' => 'Fallback', 'priority' => 99, 'required_facts' => [FactConstants::SCORE_PASS], 'action_id' => $actionIds[ActionConstants::FEEDBACK]],
            ['rule_code' => 'F02', 'name' => 'Failure Fallback', 'domain' => 'Fallback', 'priority' => 99, 'required_facts' => [FactConstants::SCORE_FAIL], 'action_id' => $actionIds[ActionConstants::FEEDBACK]],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create($rule + ['is_active' => true]);
        }

        \Illuminate\Support\Facades\Cache::forget('adaptive_rules_v7');
    }
}
