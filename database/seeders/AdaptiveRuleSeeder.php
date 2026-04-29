<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
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
        $keys = AdaptiveConditionKeys::class;

        // Map Fact ID to its JSON Logic for the 'description' column
        $logics = [
            // Accuracy (Strict Non-Overlapping Ranges)
            FactConstants::ACCURACY_CRISIS      => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_LT, AdaptiveConditionKeys::VAL => 40],
            FactConstants::ACCURACY_STRUGGLE    => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 40], // Logic simplified: Engine handles priority
            FactConstants::ACCURACY_STABLE      => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 60],
            FactConstants::ACCURACY_PROGRESSING => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 75],
            FactConstants::ACCURACY_OPTIMAL     => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 85],
            FactConstants::ACCURACY_EXCELLENT   => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 92],

            // Help
            FactConstants::HELP_HIGH => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 3],
            FactConstants::HELP_MED  => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 2],
            FactConstants::HELP_NONE => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 0],

            // Trend
            FactConstants::TREND_DOWN   => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'down'],
            FactConstants::TREND_STABLE => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'stable'],
            FactConstants::TREND_UP     => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'up'],

            // Speed
            FactConstants::TIME_FAST   => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'fast'],
            FactConstants::TIME_SLOW   => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'slow'],
            FactConstants::TIME_NORMAL => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'normal'],

            // Streak
            FactConstants::STREAK_3D => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 3],
            FactConstants::STREAK_5D => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 5],
            FactConstants::STREAK_7D => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE , AdaptiveConditionKeys::VAL => 7],

            // Level
            FactConstants::LEVEL_AHLI => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::LEVEL, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 'Ahli'],
        ];

        // Primary Facts
        foreach (FactConstants::NAMES as $id => $name) {
            AdaptiveFact::create([
                'id'          => $id,
                'category'    => 'primary',
                'name'        => $name,
                'description' => isset($logics[$id]) ? json_encode($logics[$id]) : null,
            ]);
        }

        // Virtual Facts (Diagnosis Results)
        foreach (FactConstants::VIRTUAL_NAMES as $id => $name) {
            AdaptiveFact::create([
                'id'       => $id,
                'category' => 'virtual',
                'name'     => $name,
            ]);
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
                'name'              => 'Krisis Pembelajaran',
                'recommendation'    => 'Sangat disarankan melakukan Remedial Review karena performa menurun drastis.',
                'required_fact_ids' => [FactConstants::ACCURACY_CRISIS, FactConstants::TREND_DOWN],
                'deduced_fact_ids'  => [FactConstants::V_CRISIS],
                'action_ids'        => [ActionConstants::REMEDIAL],
            ],
            // Rule Berantai: Jika Krisis DAN Butuh Banyak Bantuan
            [
                'id'                => 'R02',
                'priority'          => 2,
                'name'              => 'Intervensi Krisis Intensif',
                'recommendation'    => 'Karena krisis dan ketergantungan bantuan, kamu akan dialihkan ke review materi dasar.',
                'required_fact_ids' => [FactConstants::V_CRISIS, FactConstants::HELP_HIGH], // MENGGUNAKAN V_CRISIS HASIL R01
                'deduced_fact_ids'  => [FactConstants::V_DEPENDENCY],
                'action_ids'        => [ActionConstants::REDUCE_DIFF, ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R03',
                'priority'          => 3,
                'name'              => 'Krisis Pembelajaran',
                'recommendation'    => 'Tingkat kesulitan dikurangi agar kamu bisa lebih fokus memahami konsep.',
                'required_fact_ids' => [FactConstants::ACCURACY_CRISIS],
                'deduced_fact_ids'  => [FactConstants::V_CRISIS],
                'action_ids'        => [ActionConstants::REDUCE_DIFF, ActionConstants::SCAFFOLD_REDUCTION],
            ],

            // --- SEDANG KESULITAN (R04-R06) ---
            [
                'id'                => 'R04',
                'priority'          => 4,
                'name'              => 'Sedang Kesulitan',
                'recommendation'    => 'Materi diturunkan satu level agar pengerjaan lebih lancar.',
                'required_fact_ids' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::TIME_SLOW],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::REDUCE_DIFF],
            ],
            [
                'id'                => 'R05',
                'priority'          => 5,
                'name'              => 'Sedang Kesulitan',
                'recommendation'    => 'Disarankan mengulang materi dasar sejenak.',
                'required_fact_ids' => [FactConstants::ACCURACY_STRUGGLE, FactConstants::HELP_MED],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R06',
                'priority'          => 6,
                'name'              => 'Sedang Kesulitan',
                'recommendation'    => 'Tetap semangat! Perhatikan penjelasan pada setiap soal.',
                'required_fact_ids' => [FactConstants::ACCURACY_STABLE, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_STRUGGLING],
                'action_ids'        => [ActionConstants::FEEDBACK],
            ],

            // --- PERFORMA OPTIMAL (R07-R09) ---
            [
                'id'                => 'R07',
                'priority'          => 7,
                'name'              => 'Performa Optimal',
                'recommendation'    => 'Luar biasa! Tingkat kesulitan dinaikkan untuk tantangan baru.',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TREND_UP],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::INCREASE_DIFF],
            ],
            [
                'id'                => 'R08',
                'priority'          => 8,
                'name'              => 'Performa Optimal',
                'recommendation'    => 'Kamu sudah ahli! Siap untuk tantangan spesial?',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::LEVEL_AHLI],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::NEW_CHALLENGE],
            ],
            [
                'id'                => 'R09',
                'priority'          => 9,
                'name'              => 'Performa Optimal',
                'recommendation'    => 'Streak luar biasa! Terus pertahankan performamu.',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::STREAK_3D],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::INCREASE_DIFF, ActionConstants::STREAK_BONUS],
            ],

            // --- KETERGANTUNGAN BANTUAN (R10-R11) ---
            [
                'id'                => 'R10',
                'priority'          => 10,
                'name'              => 'Ketergantungan Bantuan',
                'recommendation'    => 'Coba kerjakan tanpa bantuan hint untuk menguji pemahaman aslimu.',
                'required_fact_ids' => [FactConstants::HELP_HIGH, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_DEPENDENCY],
                'action_ids'        => [ActionConstants::SCAFFOLD_REDUCTION, ActionConstants::REMEDIAL],
            ],
            [
                'id'                => 'R11',
                'priority'          => 11,
                'name'              => 'Ketergantungan Bantuan',
                'recommendation'    => 'Jumlah hint akan dikurangi secara bertahap agar kamu lebih mandiri.',
                'required_fact_ids' => [FactConstants::HELP_HIGH, FactConstants::TREND_UP],
                'deduced_fact_ids'  => [FactConstants::V_DEPENDENCY],
                'action_ids'        => [ActionConstants::SCAFFOLD_REDUCTION],
            ],

            // --- POTENSI KEBOSANAN (R12-R13) ---
            [
                'id'                => 'R12',
                'priority'          => 12,
                'name'              => 'Potensi Kebosanan',
                'recommendation'    => 'Waktunya tantangan baru agar belajar tidak terasa membosankan!',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::STREAK_5D, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_BOREDOM],
                'action_ids'        => [ActionConstants::NEW_CHALLENGE, ActionConstants::STREAK_BONUS],
            ],
            [
                'id'                => 'R13',
                'priority'          => 13,
                'name'              => 'Potensi Kebosanan',
                'recommendation'    => 'Kecepatanmu luar biasa! Ayo naik ke level berikutnya.',
                'required_fact_ids' => [FactConstants::ACCURACY_OPTIMAL, FactConstants::TIME_FAST, FactConstants::TREND_STABLE],
                'deduced_fact_ids'  => [FactConstants::V_BOREDOM],
                'action_ids'        => [ActionConstants::INCREASE_DIFF],
            ],

            // --- PERFORMA STABIL & BERKEMBANG (R16-R17) ---
            [
                'id'                => 'R16',
                'priority'          => 14,
                'name'              => 'Pertumbuhan Stabil',
                'recommendation'    => 'Performa kamu sedang berkembang pesat! Kami akan mulai mengurangi bantuan agar kamu lebih mandiri.',
                'required_fact_ids' => [FactConstants::ACCURACY_PROGRESSING, FactConstants::TREND_UP],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::SCAFFOLD_REDUCTION],
            ],

            // --- FALLBACK & SPECIAL (R14-R15) ---
            [
                'id'                => 'R15',
                'priority'          => 0, // Prioritas tertinggi untuk sertifikasi
                'name'              => 'Grant Certification',
                'recommendation'    => 'Selamat! Kamu berhak mendapatkan sertifikat atas dedikasi dan performamu.',
                'required_fact_ids' => [FactConstants::LEVEL_AHLI, FactConstants::ACCURACY_EXCELLENT, FactConstants::STREAK_7D, FactConstants::HELP_NONE],
                'deduced_fact_ids'  => [FactConstants::V_OPTIMAL],
                'action_ids'        => [ActionConstants::CERTIFICATION],
            ],
            [
                'id'                => 'R14',
                'priority'          => 100, // Fallback
                'name'              => 'Normal Learning',
                'recommendation'    => 'Lanjutkan belajarmu, konsistensi adalah kunci kesuksesan!',
                'required_fact_ids' => [],
                'deduced_fact_ids'  => [],
                'action_ids'        => [ActionConstants::FEEDBACK],
            ],
        ];

        foreach ($rules as $ruleData) {
            AdaptiveRule::create($ruleData + ['is_active' => true]);
        }
    }
}
