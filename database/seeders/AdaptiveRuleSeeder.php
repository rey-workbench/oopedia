<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
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
        foreach (FactConstants::NAMES as $code => $name) {
            AdaptiveFact::create(['code' => $code, 'category' => 'primary', 'name' => $name]);
        }
        foreach (FactConstants::VIRTUAL_NAMES as $code => $name) {
            AdaptiveFact::create(['code' => $code, 'category' => 'virtual', 'name' => $name]);
        }
    }

    private function seedActions(): void
    {
        $actions = [
            [
                'code' => ActionConstants::REMEDIAL,
                'name' => 'Remedial Review',
                'description' => 'Ulangi materi dasar + soal mudah',
                'variant' => 'danger'
            ],
            [
                'code' => ActionConstants::REDUCE_DIFF,
                'name' => 'Reduce Difficulty',
                'description' => 'Turunkan 1 level kesulitan',
                'variant' => 'warning'
            ],
            [
                'code' => ActionConstants::INCREASE_DIFF,
                'name' => 'Increase Difficulty',
                'description' => 'Naikkan 1 level kesulitan',
                'variant' => 'success'
            ],
            [
                'code' => ActionConstants::SCAFFOLD_REDUCTION,
                'name' => 'Scaffold Reduction',
                'description' => 'Kurangi hint bertahap',
                'variant' => 'info'
            ],
            [
                'code' => ActionConstants::NEW_CHALLENGE,
                'name' => 'New Challenge',
                'description' => 'Variasi soal + studi kasus',
                'variant' => 'primary'
            ],
            [
                'code' => ActionConstants::FEEDBACK,
                'name' => 'General Feedback',
                'description' => 'Umpan balik motivasi & ringkasan',
                'variant' => 'secondary'
            ],
            [
                'code' => ActionConstants::STREAK_BONUS,
                'name' => 'Streak Bonus',
                'description' => 'Berikan XP tambahan & badge',
                'variant' => 'success'
            ],
            [
                'code' => ActionConstants::CERTIFICATION,
                'name' => 'Grant Certification',
                'description' => 'Sertifikat kelulusan ahli',
                'variant' => 'success'
            ],
        ];

        foreach ($actions as $action) {
            AdaptiveAction::create($action + [
                'instructions' => []
            ]);
        }
    }

    private function seedRules(): void
    {
        $rules = [
            // --- KRISIS PEMBELAJARAN (R01-R03) ---
            [
                'code' => 'R01',
                'priority' => 1,
                'name' => 'Krisis (Severe)',
                'domain' => 'Crisis',
                'required_facts' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_DOWN, FactConstants::HELP_HIGH],
                'deduced_facts' => [FactConstants::V_CRISIS],
                'action_codes' => [ActionConstants::REMEDIAL, ActionConstants::REDUCE_DIFF]
            ],
            [
                'code' => 'R02',
                'priority' => 2,
                'name' => 'Krisis (Standard)',
                'domain' => 'Crisis',
                'required_facts' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_DOWN, FactConstants::HELP_MED],
                'deduced_facts' => [FactConstants::V_CRISIS],
                'action_codes' => [ActionConstants::REMEDIAL]
            ],
            [
                'code' => 'R03',
                'priority' => 3,
                'name' => 'Krisis (Improving)',
                'domain' => 'Crisis',
                'required_facts' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_UP, FactConstants::HELP_HIGH],
                'deduced_facts' => [FactConstants::V_CRISIS],
                'action_codes' => [ActionConstants::REDUCE_DIFF, ActionConstants::SCAFFOLD_REDUCTION]
            ],

            // --- SEDANG KESULITAN (R04-R06) ---
            [
                'code' => 'R04',
                'priority' => 4,
                'name' => 'Struggling (Slow)',
                'domain' => 'Struggling',
                'required_facts' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::TIME_SLOW],
                'deduced_facts' => [FactConstants::V_STRUGGLING],
                'action_codes' => [ActionConstants::REDUCE_DIFF]
            ],
            [
                'code' => 'R05',
                'priority' => 5,
                'name' => 'Struggling (Normal)',
                'domain' => 'Struggling',
                'required_facts' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::TIME_NORMAL, FactConstants::HELP_MED],
                'deduced_facts' => [FactConstants::V_STRUGGLING],
                'action_codes' => [ActionConstants::REMEDIAL]
            ],
            [
                'code' => 'R06',
                'priority' => 6,
                'name' => 'Struggling (Stable)',
                'domain' => 'Struggling',
                'required_facts' => [FactConstants::ACCURACY_STABLE, FactConstants::TREND_STABLE],
                'deduced_facts' => [FactConstants::V_STRUGGLING],
                'action_codes' => [ActionConstants::FEEDBACK]
            ],

            // --- PERFORMA OPTIMAL (R07-R09) ---
            [
                'code' => 'R07',
                'priority' => 7,
                'name' => 'Optimal (Promotion)',
                'domain' => 'Optimal',
                'required_facts' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TREND_UP],
                'deduced_facts' => [FactConstants::V_OPTIMAL],
                'action_codes' => [ActionConstants::INCREASE_DIFF]
            ],
            [
                'code' => 'R08',
                'priority' => 8,
                'name' => 'Optimal (Ahli)',
                'domain' => 'Optimal',
                'required_facts' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::LEVEL_AHLI],
                'deduced_facts' => [FactConstants::V_OPTIMAL],
                'action_codes' => [ActionConstants::NEW_CHALLENGE]
            ],
            [
                'code' => 'R09',
                'priority' => 9,
                'name' => 'Optimal (Streak)',
                'domain' => 'Optimal',
                'required_facts' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TIME_FAST, FactConstants::STREAK_3D],
                'deduced_facts' => [FactConstants::V_OPTIMAL],
                'action_codes' => [ActionConstants::INCREASE_DIFF, ActionConstants::STREAK_BONUS]
            ],

            // --- KETERGANTUNGAN BANTUAN (R10-R11) ---
            [
                'code' => 'R10',
                'priority' => 10,
                'name' => 'Dependency (Low)',
                'domain' => 'Dependency',
                'required_facts' => [FactConstants::HELP_HIGH, FactConstants::TREND_STABLE],
                'deduced_facts' => [FactConstants::V_DEPENDENCY],
                'action_codes' => [ActionConstants::SCAFFOLD_REDUCTION, ActionConstants::REMEDIAL]
            ],
            [
                'code' => 'R11',
                'priority' => 11,
                'name' => 'Dependency (High)',
                'domain' => 'Dependency',
                'required_facts' => [FactConstants::HELP_HIGH, FactConstants::TREND_UP],
                'deduced_facts' => [FactConstants::V_DEPENDENCY],
                'action_codes' => [ActionConstants::SCAFFOLD_REDUCTION]
            ],

            // --- POTENSI KEBOSANAN (R12-R13) ---
            [
                'code' => 'R12',
                'priority' => 12,
                'name' => 'Boredom (Stagnant)',
                'domain' => 'Boredom',
                'required_facts' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::STREAK_5D, FactConstants::TREND_STABLE],
                'deduced_facts' => [FactConstants::V_BOREDOM],
                'action_codes' => [ActionConstants::NEW_CHALLENGE, ActionConstants::STREAK_BONUS]
            ],
            [
                'code' => 'R13',
                'priority' => 13,
                'name' => 'Boredom (Fast)',
                'domain' => 'Boredom',
                'required_facts' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TIME_FAST, FactConstants::TREND_STABLE],
                'deduced_facts' => [FactConstants::V_BOREDOM],
                'action_codes' => [ActionConstants::INCREASE_DIFF]
            ],

            // --- SPECIAL & FALLBACK (R14-R15) ---
            [
                'code' => 'R14',
                'priority' => 100,
                'name' => 'Default Fallback',
                'domain' => 'Fallback',
                'required_facts' => [],
                'deduced_facts' => [],
                'action_codes' => [ActionConstants::FEEDBACK]
            ],
            [
                'code' => 'R15',
                'priority' => 0, // Highest priority? No, usually certification checked if other rules don't match or specific.
                'name' => 'Grant Certification',
                'domain' => 'Special',
                'required_facts' => [FactConstants::LEVEL_AHLI, FactConstants::ACCURACY_OPTIMAL, FactConstants::STREAK_7D, FactConstants::HELP_NONE],
                'deduced_facts' => [],
                'action_codes' => [ActionConstants::CERTIFICATION]
            ],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create($rule + ['is_active' => true]);
        }
    }
}
