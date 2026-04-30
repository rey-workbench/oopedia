<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConditionKeys;
use App\Rules\Adaptive\Constants\AdaptiveMetadataKeys;
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
        // 1. Primary Facts (G-codes)
        $primaryFacts = [
            'G01' => ['name' => 'Akurasi <40%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_LT, AdaptiveConditionKeys::VAL => 40]],
            'G02' => ['name' => 'Akurasi 40-60%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_LTE, AdaptiveConditionKeys::VAL => 60]],
            'G03' => ['name' => 'Akurasi 60-70%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_LTE, AdaptiveConditionKeys::VAL => 70]],
            'G18' => ['name' => 'Akurasi 70-80%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_LTE, AdaptiveConditionKeys::VAL => 80]],
            'G04' => ['name' => 'Akurasi >80%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 80]],
            'G17' => ['name' => 'Akurasi >85%', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::ACCURACY, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 85]],
            'G08' => ['name' => 'Bantuan >3x', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GT, AdaptiveConditionKeys::VAL => 3]],
            'G09' => ['name' => 'Bantuan 2-3x', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 2]],
            'G20' => ['name' => 'Bantuan 0x', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::HINTS, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => 0]],
            'G05' => ['name' => 'Tren Turun', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::TREND_DOWN]],
            'G06' => ['name' => 'Tren Stabil', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::TREND_STABLE]],
            'G07' => ['name' => 'Tren Naik', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::TREND, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::TREND_UP]],
            'G11' => ['name' => 'Respon Cepat', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::SPEED_FAST]],
            'G12' => ['name' => 'Respon Lambat', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::SPEED_SLOW]],
            'G13' => ['name' => 'Respon Normal', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::SPEED, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::SPEED_NORMAL]],
            'G14' => ['name' => 'Streak >=3 Hari', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 3]],
            'G15' => ['name' => 'Streak >=5 Hari', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 5]],
            'G16' => ['name' => 'Streak >=7 Hari', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::STREAK, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_GTE, AdaptiveConditionKeys::VAL => 7]],
            'G19' => ['name' => 'Level Ahli', 'logic' => [AdaptiveConditionKeys::KEY => AdaptiveConditionKeys::LEVEL, AdaptiveConditionKeys::OP => AdaptiveConditionKeys::OP_EQ, AdaptiveConditionKeys::VAL => AdaptiveConditionKeys::LEVEL_AHLI]],
        ];

        foreach ($primaryFacts as $id => $data) {
            AdaptiveFact::create([
                'id'       => $id,
                'category' => 'primary',
                'name'     => $data['name'],
                'logic'    => json_encode($data['logic']),
            ]);
        }

        // 2. Virtual Facts (V-codes / Diagnosis)
        $virtualFacts = [
            'V01' => 'Krisis Pembelajaran',
            'V02' => 'Sedang Kesulitan',
            'V03' => 'Performa Optimal',
            'V04' => 'Ketergantungan Bantuan',
            'V05' => 'Potensi Kebosanan',
        ];

        foreach ($virtualFacts as $id => $name) {
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
            ['id' => 'REMEDIAL', 'name' => 'Remedial Review', 'description' => 'ulangi materi dasar', 'variant' => 'danger'],
            ['id' => 'REDUCE_DIFF', 'name' => 'Reduce Difficulty', 'description' => 'turunkan 1 level', 'variant' => 'warning'],
            ['id' => 'INCREASE_DIFF', 'name' => 'Increase Difficulty', 'description' => '+1 langkah bertahap', 'variant' => 'success'],
            ['id' => 'SCAFFOLD_REDUCTION', 'name' => 'Scaffold Reduction', 'description' => 'kurangi hint bertahap', 'variant' => 'info'],
            ['id' => 'NEW_CHALLENGE', 'name' => 'New Challenge', 'description' => 'variasi + streak bonus', 'variant' => 'primary'],
            ['id' => 'FEEDBACK', 'name' => 'Default Fallback', 'description' => 'general feedback', 'variant' => 'secondary'],
            ['id' => 'STREAK_BONUS', 'name' => 'Streak Bonus', 'description' => 'XP tambahan (Internal)', 'variant' => 'success'],
            ['id' => 'CERTIFICATION', 'name' => 'Grant Certification ★', 'description' => 'level ahli + 3 sesi >85% + streak 7+', 'variant' => 'success'],
        ];

        foreach ($actions as $action) {
            AdaptiveAction::create($action + ['instructions' => []]);
        }
    }

    private function seedRules(): void
    {
        $m = AdaptiveMetadataKeys::class;

        $rules = [
            // Krisis
            [
                'id'                => 'R01',
                'priority'          => 1,
                'name'              => 'Diagnosis Krisis Pembelajaran',
                'recommendation'    => 'Mendeteksi kondisi kritis.',
                'required_fact_ids' => ['G01', 'G05'],
                'deduced_fact_ids'  => ['V01'],
                'actions'           => [],
            ],
            [
                'id'                => 'R02',
                'priority'          => 2,
                'name'              => 'Intervensi Krisis Intensif',
                'recommendation'    => 'Sangat disarankan Remedial Review.',
                'required_fact_ids' => ['V01', 'G08'],
                'deduced_fact_ids'  => ['V04'],
                'actions'           => [
                    ['id' => 'REMEDIAL', 'metadata' => [$m::TARGET_DIFFICULTY => 'beginner', $m::FORCED_EASY_COUNT => 5, $m::SHOW_MOTIVATION => true]],
                    ['id' => 'REDUCE_DIFF', 'metadata' => []],
                ],
            ],
            [
                'id'                => 'R03',
                'priority'          => 3,
                'name'              => 'Intervensi Krisis Standar',
                'recommendation'    => 'Ulangi materi dasar.',
                'required_fact_ids' => ['V01'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'REMEDIAL', 'metadata' => []],
                ],
            ],
            // Kesulitan
            [
                'id'                => 'R04',
                'priority'          => 4,
                'name'              => 'Diagnosis Kesulitan Materi',
                'recommendation'    => 'Mendeteksi hambatan belajar.',
                'required_fact_ids' => ['G02'],
                'deduced_fact_ids'  => ['V02'],
                'actions'           => [],
            ],
            [
                'id'                => 'R05',
                'priority'          => 5,
                'name'              => 'Penyesuaian Tingkat Kesulitan',
                'recommendation'    => 'Materi diturunkan satu level.',
                'required_fact_ids' => ['V02', 'G12'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'REDUCE_DIFF', 'metadata' => []],
                ],
            ],
            [
                'id'                => 'R06',
                'priority'          => 6,
                'name'              => 'Pendampingan Belajar',
                'recommendation'    => 'Tetap semangat! Perhatikan penjelasan.',
                'required_fact_ids' => ['V02', 'G06'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'FEEDBACK', 'metadata' => []],
                ],
            ],
            // Optimal
            [
                'id'                => 'R07',
                'priority'          => 7,
                'name'              => 'Diagnosis Performa Optimal',
                'recommendation'    => 'Siap untuk tantangan tinggi.',
                'required_fact_ids' => ['G04', 'G07'],
                'deduced_fact_ids'  => ['V03'],
                'actions'           => [
                    ['id' => 'INCREASE_DIFF', 'metadata' => [$m::DIFFICULTY_STEPS => 1]],
                ],
            ],
            [
                'id'                => 'R08',
                'priority'          => 8,
                'name'              => 'Tantangan Ahli',
                'recommendation'    => 'Siap tantangan studi kasus?',
                'required_fact_ids' => ['V03', 'G19'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'NEW_CHALLENGE', 'metadata' => [$m::CHECK_CERTIFICATION => true]],
                ],
            ],
            [
                'id'                => 'R09',
                'priority'          => 9,
                'name'              => 'Apresiasi Capaian',
                'recommendation'    => 'Streak luar biasa! Terus pertahankan.',
                'required_fact_ids' => ['V03', 'G14'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'STREAK_BONUS', 'metadata' => []],
                ],
            ],
            // Ketergantungan
            [
                'id'                => 'R10',
                'priority'          => 10,
                'name'              => 'Diagnosis Ketergantungan Bantuan',
                'recommendation'    => 'Penggunaan bantuan berlebihan.',
                'required_fact_ids' => ['G08'],
                'deduced_fact_ids'  => ['V04'],
                'actions'           => [],
            ],
            [
                'id'                => 'R11',
                'priority'          => 11,
                'name'              => 'Latihan Kemandirian',
                'recommendation'    => 'Hint dikurangi bertahap.',
                'required_fact_ids' => ['V04', 'G07'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'SCAFFOLD_REDUCTION', 'metadata' => [$m::GRADUAL_SCAFFOLD_REDUCTION => true]],
                ],
            ],
            // Kebosanan
            [
                'id'                => 'R12',
                'priority'          => 12,
                'name'              => 'Diagnosis Potensi Kebosanan',
                'recommendation'    => 'Mendeteksi kebosanan.',
                'required_fact_ids' => ['G04', 'G15', 'G06'],
                'deduced_fact_ids'  => ['V05'],
                'actions'           => [],
            ],
            [
                'id'                => 'R13',
                'priority'          => 13,
                'name'              => 'Pemberian Variasi Materi',
                'recommendation'    => 'Waktunya tantangan lintas topik!',
                'required_fact_ids' => ['V05'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'NEW_CHALLENGE', 'metadata' => [$m::CROSS_TOPIC_CHALLENGE => true]],
                ],
            ],
            // Special
            [
                'id'                => 'R15',
                'priority'          => 0,
                'name'              => '★ Grant Certification',
                'recommendation'    => 'Selamat mendapatkan sertifikat!',
                'required_fact_ids' => ['V03', 'G17', 'G16', 'G20'],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'CERTIFICATION', 'metadata' => [$m::NOTIFY_TEACHER => true, $m::NOTIFY_TYPE => $m::TYPE_CERTIFICATION]],
                ],
            ],
            [
                'id'                => 'R14',
                'priority'          => 100,
                'name'              => 'Normal Learning',
                'recommendation'    => 'Lanjutkan belajarmu!',
                'required_fact_ids' => [],
                'deduced_fact_ids'  => [],
                'actions'           => [
                    ['id' => 'FEEDBACK', 'metadata' => []],
                ],
            ],
        ];

        foreach ($rules as $ruleData) {
            AdaptiveRule::create($ruleData + ['is_active' => true]);
        }
    }
}
