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
        foreach (FactConstants::NAMES as $id => $name) {
            AdaptiveFact::create(['id' => $id, 'category' => 'primary', 'name' => $name]);
        }
        foreach (FactConstants::VIRTUAL_NAMES as $id => $name) {
            AdaptiveFact::create(['id' => $id, 'category' => 'virtual', 'name' => $name]);
        }
    }

    private function seedActions(): void
    {
        $actions = [
            [
                'id'          => ActionConstants::REMEDIAL,
                'name'        => 'Remedial Review',
                'description' => 'ulangi materi dasar',
                'variant'     => 'danger',
            ],
            [
                'id'          => ActionConstants::REDUCE_DIFF,
                'name'        => 'Reduce Difficulty',
                'description' => 'turunkan 1 level',
                'variant'     => 'warning',
            ],
            [
                'id'          => ActionConstants::INCREASE_DIFF,
                'name'        => 'Increase Difficulty',
                'description' => '+1 langkah bertahap',
                'variant'     => 'success',
            ],
            [
                'id'          => ActionConstants::SCAFFOLD_REDUCTION,
                'name'        => 'Scaffold Reduction',
                'description' => 'kurangi hint bertahap',
                'variant'     => 'info',
            ],
            [
                'id'          => ActionConstants::NEW_CHALLENGE,
                'name'        => 'New Challenge',
                'description' => 'variasi + streak bonus',
                'variant'     => 'primary',
            ],
            [
                'id'          => ActionConstants::FEEDBACK,
                'name'        => 'Default Fallback',
                'description' => 'general feedback',
                'variant'     => 'secondary',
            ],
            [
                'id'          => ActionConstants::STREAK_BONUS,
                'name'        => 'Streak Bonus',
                'description' => 'XP tambahan (Internal)',
                'variant'     => 'success',
            ],
            [
                'id'          => ActionConstants::CERTIFICATION,
                'name'        => 'Grant Certification ★',
                'description' => 'level ahli + 3 sesi >85% + streak 7+',
                'variant'     => 'success',
            ],
        ];

        foreach ($actions as $action) {
            AdaptiveAction::create($action + [
                'instructions' => [],
            ]);
        }
    }

    private function seedRules(): void
    {
        $rules = [
            // --- KRISIS PEMBELAJARAN (R01-R03) ---
            [
                'id'                => 'R01',
                'priority'          => 1,
                'name'              => 'Krisis (Severe)',
                'domain'            => 'Crisis',
                'required_fact_ids' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_DOWN, FactConstants::HELP_HIGH],
                'deduced_fact_ids'  => [FactConstants::V_CRISIS],
                'action_ids'        => [ActionConstants::REMEDIAL, ActionConstants::REDUCE_DIFF],
            ],
            [
                'id'                => 'R02',
                'priority'          => 2,
                'name'              => 'Krisis (Standard)',
                'domain'            => 'Crisis',
                'required_fact_ids' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_DOWN, FactConstants::HELP_MED],
                'deduced_fact_ids'  => [FactConstants::V_CRISIS],
                'action_ids'        => [ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R03',
                'priority'          => 3,
                'name'              => 'Krisis (Improving)',
                'domain'            => 'Crisis',
                'required_fact_ids' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_UP, FactConstants::HELP_HIGH],
                'deduced_fact_ids'  => [FactConstants::V_CRISIS],
                'action_ids'        => [ActionConstants::REDUCE_DIFF, ActionConstants::SCAFFOLD_REDUCTION],
            ],

            // --- SEDANG KESULITAN (R04-R06) ---
            [
                'id'                => 'R04',
                'priority'          => 4,
                'name'              => 'Struggling (Slow)',
                'domain'            => 'Struggling',
                'required_fact_ids' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::TIME_SLOW],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::REDUCE_DIFF],
            ],
            [
                'id'                => 'R05',
                'priority'          => 5,
                'name'              => 'Struggling (Normal)',
                'domain'            => 'Struggling',
                'required_fact_ids' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::TIME_NORMAL, FactConstants::HELP_MED],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R06',
                'priority'          => 6,
                'name'              => 'Struggling (Stable)',
                'domain'            => 'Struggling',
                'required_fact_ids' => [FactConstants::ACCURACY_STABLE, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::FEEDBACK],
            ],

            // --- PERFORMA OPTIMAL (R07-R09) ---
            [
                'id'                => 'R07',
                'priority'          => 7,
                'name'              => 'Optimal (Promotion)',
                'domain'            => 'Optimal',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TREND_UP],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::INCREASE_DIFF],
            ],
            [
                'id'                => 'R08',
                'priority'          => 8,
                'name'              => 'Optimal (Ahli)',
                'domain'            => 'Optimal',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::LEVEL_AHLI],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::NEW_CHALLENGE],
            ],
            [
                'id'                => 'R09',
                'priority'          => 9,
                'name'              => 'Optimal (Streak)',
                'domain'            => 'Optimal',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TIME_FAST, FactConstants::STREAK_3D],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::INCREASE_DIFF, ActionConstants::STREAK_BONUS],
            ],

            // --- KETERGANTUNGAN BANTUAN (R10-R11) ---
            [
                'id'                => 'R10',
                'priority'          => 10,
                'name'              => 'Dependency (Low)',
                'domain'            => 'Dependency',
                'required_fact_ids' => [FactConstants::HELP_HIGH, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_DEPENDENCY],
                'action_ids'        => [ActionConstants::SCAFFOLD_REDUCTION, ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R11',
                'priority'          => 11,
                'name'              => 'Dependency (High)',
                'domain'            => 'Dependency',
                'required_fact_ids' => [FactConstants::HELP_HIGH, FactConstants::TREND_UP],
                'deduced_fact_ids'  => [FactConstants::V_DEPENDENCY],
                'action_ids'        => [ActionConstants::SCAFFOLD_REDUCTION],
            ],

            // --- POTENSI KEBOSANAN (R12-R13) ---
            [
                'id'                => 'R12',
                'priority'          => 12,
                'name'              => 'Boredom (Stagnant)',
                'domain'            => 'Boredom',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::STREAK_5D, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_BOREDOM],
                'action_ids'        => [ActionConstants::NEW_CHALLENGE, ActionConstants::STREAK_BONUS],
            ],
            [
                'id'                => 'R13',
                'priority'          => 13,
                'name'              => 'Boredom (Fast)',
                'domain'            => 'Boredom',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TIME_FAST, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_BOREDOM],
                'action_ids'        => [ActionConstants::INCREASE_DIFF],
            ],

            // --- SPECIAL & FALLBACK (R14-R15) ---
            [
                'id'                => 'R14',
                'priority'          => 100,
                'name'              => 'Default Fallback',
                'domain'            => 'Fallback',
                'required_fact_ids' => [],
                'deduced_fact_ids'  => [],
                'action_ids'        => [ActionConstants::FEEDBACK],
            ],
            [
                'id'                => 'R15',
                'priority'          => 0,
                'name'              => 'Grant Certification',
                'domain'            => 'Special',
                'required_fact_ids' => [FactConstants::LEVEL_AHLI, FactConstants::ACCURACY_EXCELLENT, FactConstants::STREAK_7D, FactConstants::HELP_NONE],
                'deduced_fact_ids'  => [],
                'action_ids'        => [ActionConstants::CERTIFICATION],
            ],
        ];

        foreach ($rules as $ruleData) {
            AdaptiveRule::create($ruleData + ['is_active' => true]);
        }
    }
}
