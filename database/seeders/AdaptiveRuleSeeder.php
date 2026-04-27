<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
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
            ['code' => FactConstants::V_MASTERY_BEGINNER, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_BEGINNER]],
            ['code' => FactConstants::V_MASTERY_MEDIUM, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_MEDIUM]],
            ['code' => FactConstants::V_MASTERY_HARD, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_HARD]],
        ];

        foreach ($facts as $fact) {
            AdaptiveFact::create($fact);
        }
    }

    private function seedActions(): void
    {
        $actions = [
            [
                'code'         => ActionConstants::FEEDBACK,
                'variant'      => 'info',
                'name'         => ActionConstants::NAMES[ActionConstants::FEEDBACK],
                'description'  => 'Aksi universal untuk semua jenis pesan.',
                'instructions' => [ActionConstants::KEY_FLOW => ActionConstants::FLOW_NEXT],
            ],
            [
                'code'         => ActionConstants::INCREASE_DIFF,
                'variant'      => 'acceleration',
                'name'         => ActionConstants::NAMES[ActionConstants::INCREASE_DIFF],
                'description'  => 'Meningkatkan tantangan belajar.',
                'instructions' => [
                    ActionConstants::KEY_FLOW             => ActionConstants::FLOW_UP,
                    StudentStateSchema::TARGET_DIFFICULTY => 'next',
                ],
            ],
            [
                'code'         => ActionConstants::REDUCE_DIFF,
                'variant'      => 'recovery',
                'name'         => ActionConstants::NAMES[ActionConstants::REDUCE_DIFF],
                'description'  => 'Menurunkan tantangan belajar.',
                'instructions' => [
                    ActionConstants::KEY_FLOW             => ActionConstants::FLOW_DOWN,
                    StudentStateSchema::TARGET_DIFFICULTY => 'prev',
                ],
            ],
            [
                'code'         => ActionConstants::STREAK_BONUS,
                'variant'      => 'gamification',
                'name'         => ActionConstants::NAMES[ActionConstants::STREAK_BONUS],
                'description'  => 'Pemberian XP tambahan.',
                'instructions' => [
                    ActionConstants::KEY_FLOW     => ActionConstants::FLOW_NONE,
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(25),
                ],
            ],
            [
                'code'         => ActionConstants::REMEDIAL,
                'variant'      => 'danger',
                'name'         => ActionConstants::NAMES[ActionConstants::REMEDIAL],
                'description'  => 'Mengarahkan siswa kembali ke materi belajar.',
                'instructions' => [ActionConstants::KEY_FLOW => ActionConstants::FLOW_REVIEW],
            ],
            [
                'code'         => ActionConstants::CERTIFICATION,
                'variant'      => 'success',
                'name'         => ActionConstants::NAMES[ActionConstants::CERTIFICATION],
                'description'  => 'Pemberian sertifikat atas penguasaan materi maksimal.',
                'instructions' => [
                    ActionConstants::KEY_TITLE         => 'Selamat! Sertifikat Diraih',
                    ActionConstants::KEY_MESSAGE       => 'Kamu telah menguasai materi ini dengan sempurna. Sertifikat digital telah diterbitkan.',
                    ActionConstants::KEY_FLOW          => ActionConstants::FLOW_FINISH,
                    ActionConstants::KEY_CERTIFICATION => true,
                    StudentStateSchema::GLOBAL_XP      => ActionConstants::inc(250),
                ],
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
            ['rule_code' => 'D08', 'name' => 'Deduce Mastery Beginner', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::DIFF_BEGINNER], 'deduced_facts' => [FactConstants::V_MASTERY_BEGINNER]],
            ['rule_code' => 'D09', 'name' => 'Deduce Mastery Medium', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::DIFF_MEDIUM], 'deduced_facts' => [FactConstants::V_MASTERY_MEDIUM]],
            ['rule_code' => 'D10', 'name' => 'Deduce Mastery Hard', 'domain' => 'Deduction', 'priority' => 0, 'required_facts' => [FactConstants::SCORE_PASS, FactConstants::DIFF_HARD], 'deduced_facts' => [FactConstants::V_MASTERY_HARD]],

            // ─── PROMOTION & CHALLENGE ──────────────────────────────────────
            [
                'rule_code'      => 'R01',
                'name'           => 'Excellent Mastery Promotion',
                'domain'         => 'Promotion',
                'priority'       => 1,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::V_MASTERY_BEGINNER],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            [
                'rule_code'      => 'R02',
                'name'           => 'Steady Progress Promotion',
                'domain'         => 'Promotion',
                'priority'       => 5,
                'required_facts' => [FactConstants::V_STEADY_LEARNER, FactConstants::V_MASTERY_BEGINNER],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            [
                'rule_code'      => 'R03',
                'name'           => 'Mastery Medium Promotion',
                'domain'         => 'Promotion',
                'priority'       => 1,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::V_MASTERY_MEDIUM],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            [
                'rule_code'      => 'R04',
                'name'           => 'Steady Mastery Challenge',
                'domain'         => 'Promotion',
                'priority'       => 8,
                'required_facts' => [FactConstants::V_STEADY_LEARNER, FactConstants::V_MASTERY_MEDIUM],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],

            // ─── SAFETY NETS & INTERVENTIONS ────────────────────────────────
            [
                'rule_code'      => 'R05',
                'name'           => 'Crisis Emergency Response',
                'domain'         => 'SafetyNet',
                'priority'       => -10,
                'required_facts' => [FactConstants::V_CRISIS_STATE],
                'action_id'      => $actionIds[ActionConstants::REMEDIAL],
            ],
            [
                'rule_code'      => 'R06',
                'name'           => 'Struggling Safety Net',
                'domain'         => 'SafetyNet',
                'priority'       => -5,
                'required_facts' => [FactConstants::V_STRUGGLING],
                'action_id'      => $actionIds[ActionConstants::REDUCE_DIFF],
            ],
            [
                'rule_code'      => 'R07',
                'name'           => 'Double Trouble (Struggle + Addiction)',
                'domain'         => 'SafetyNet',
                'priority'       => -8,
                'required_facts' => [FactConstants::V_STRUGGLING, FactConstants::V_HINT_DEPENDENT],
                'action_id'      => $actionIds[ActionConstants::REDUCE_DIFF],
            ],
            [
                'rule_code'      => 'R08',
                'name'           => 'Unfocused Crisis Warning',
                'domain'         => 'SafetyNet',
                'priority'       => -9,
                'required_facts' => [FactConstants::V_UNFOCUSED, FactConstants::V_CRISIS_STATE],
                'action_id'      => $actionIds[ActionConstants::REMEDIAL],
            ],

            // ─── BEHAVIORAL ADJUSTMENTS ─────────────────────────────────────
            [
                'rule_code'      => 'R09',
                'name'           => 'Boredom Escape Challenge',
                'domain'         => 'Behavior',
                'priority'       => 15,
                'required_facts' => [FactConstants::V_BOREDOM_DETECTED],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            [
                'rule_code'      => 'R10',
                'name'           => 'Steady Student Boredom',
                'domain'         => 'Behavior',
                'priority'       => 12,
                'required_facts' => [FactConstants::V_STEADY_LEARNER, FactConstants::V_BOREDOM_DETECTED],
                'action_id'      => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            [
                'rule_code'      => 'R11',
                'name'           => 'Hint Addiction Prevention',
                'domain'         => 'Behavior',
                'priority'       => 20,
                'required_facts' => [FactConstants::V_HINT_DEPENDENT],
                'action_id'      => $actionIds[ActionConstants::FEEDBACK],
            ],
            [
                'rule_code'      => 'R12',
                'name'           => 'Unfocused Redirection',
                'domain'         => 'Behavior',
                'priority'       => 22,
                'required_facts' => [FactConstants::V_UNFOCUSED],
                'action_id'      => $actionIds[ActionConstants::FEEDBACK],
            ],

            // ─── REWARDS & MOTIVATION ───────────────────────────────────────
            [
                'rule_code'      => 'R13',
                'name'           => 'Perfect Mastery Reward',
                'domain'         => 'Reward',
                'priority'       => 25,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::V_STEADY_LEARNER],
                'action_id'      => $actionIds[ActionConstants::STREAK_BONUS],
            ],
            [
                'rule_code'      => 'R14',
                'name'           => 'Recovery Achievement',
                'domain'         => 'Reward',
                'priority'       => 30,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::V_CRISIS_STATE],
                'action_id'      => $actionIds[ActionConstants::FEEDBACK],
            ],

            // ─── CERTIFICATION ──────────────────────────────────────────────
            [
                'rule_code'      => 'R15',
                'name'           => 'Module Graduation (Certificate)',
                'domain'         => 'Certification',
                'priority'       => 0, // High priority achievement
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT, FactConstants::V_MASTERY_HARD],
                'action_id'      => $actionIds[ActionConstants::CERTIFICATION],
            ],

            // ─── FALLBACKS ───────────────────────────────────────────────────
            ['rule_code' => 'F01', 'name' => 'Standard Pass Feedback', 'domain' => 'Fallback', 'priority' => 99, 'required_facts' => [FactConstants::SCORE_PASS], 'action_id' => $actionIds[ActionConstants::FEEDBACK]],
            ['rule_code' => 'F02', 'name' => 'Standard Fail Support', 'domain' => 'Fallback', 'priority' => 99, 'required_facts' => [FactConstants::SCORE_FAIL], 'action_id' => $actionIds[ActionConstants::FEEDBACK]],
        ];

        AdaptiveRule::query()->delete();
        foreach ($rules as $rule) {
            AdaptiveRule::create($rule + ['is_active' => true]);
        }

        Cache::forget('adaptive_rules_v7');
    }
}
