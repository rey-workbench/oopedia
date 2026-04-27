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
            ['code' => FactConstants::SCORE_FAIL, 'category' => 'performance', 'name' => FactConstants::NAMES[FactConstants::SCORE_FAIL], 'description' => 'Siswa menjawab salah.'],
            ['code' => FactConstants::SCORE_PASS, 'category' => 'performance', 'name' => FactConstants::NAMES[FactConstants::SCORE_PASS], 'description' => 'Siswa menjawab benar.'],
            ['code' => FactConstants::TIME_QUICK, 'category' => 'time', 'name' => FactConstants::NAMES[FactConstants::TIME_QUICK], 'description' => 'Waktu pengerjaan cepat.'],
            ['code' => FactConstants::TIME_SLOW, 'category' => 'time', 'name' => FactConstants::NAMES[FactConstants::TIME_SLOW], 'description' => 'Waktu pengerjaan lambat.'],
            ['code' => FactConstants::HINT_USED, 'category' => 'behaviour', 'name' => FactConstants::NAMES[FactConstants::HINT_USED], 'description' => 'Menggunakan bantuan hint.'],
            ['code' => FactConstants::V_EXCELLENT_RESULT, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_EXCELLENT_RESULT], 'description' => 'Hasil sangat baik dan cepat.'],
            ['code' => FactConstants::V_STRUGGLING, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_STRUGGLING], 'description' => 'Mahasiswa sedang kesulitan.'],
            ['code' => FactConstants::V_STEADY_LEARNER, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_STEADY_LEARNER], 'description' => 'Belajar dengan teliti.'],
            ['code' => FactConstants::V_UNFOCUSED, 'category' => 'virtual', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_UNFOCUSED], 'description' => 'Kurang fokus.'],
            ['code' => FactConstants::V_MASTERY_BEGINNER, 'category' => 'mastery', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_BEGINNER], 'description' => 'Penguasaan materi tingkat pemula.'],
            ['code' => FactConstants::V_MASTERY_MEDIUM, 'category' => 'mastery', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_MEDIUM], 'description' => 'Penguasaan materi tingkat menengah.'],
            ['code' => FactConstants::V_MASTERY_HARD, 'category' => 'mastery', 'name' => FactConstants::VIRTUAL_NAMES[FactConstants::V_MASTERY_HARD], 'description' => 'Penguasaan materi tingkat ahli.'],
        ];

        foreach ($facts as $fact) {
            AdaptiveFact::create($fact);
        }
    }

    private function seedActions(): void
    {
        $actions = [
            [
                'code' => ActionConstants::DEDUCTION,
                'variant' => 'silent',
                'name' => ActionConstants::NAMES[ActionConstants::DEDUCTION],
                'description' => 'Aksi tanpa efek samping (hanya deduksi fakta).',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NONE,
                ]
            ],
            [
                'code' => ActionConstants::NEXT_QUESTION,
                'variant' => 'result',
                'name' => ActionConstants::NAMES[ActionConstants::NEXT_QUESTION],
                'description' => 'Lanjut ke pertanyaan berikutnya tanpa perubahan kesulitan.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NEXT,
                    ActionConstants::KEY_TITLE => 'Bagus!',
                    ActionConstants::KEY_MESSAGE => 'Jawabanmu benar. Silakan lanjut ke soal berikutnya.',
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(PedagogicalConstants::XP_REWARD_BASE),
                    StudentStateSchema::CURRENT_STREAK => ActionConstants::inc(1),
                    StudentStateSchema::TOTAL_QUESTIONS_ANSWERED => ActionConstants::inc(1)
                ]
            ],
            [
                'code' => ActionConstants::INCREASE_DIFF,
                'variant' => 'result',
                'name' => ActionConstants::NAMES[ActionConstants::INCREASE_DIFF],
                'description' => 'Tingkatkan tingkat kesulitan soal.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_UP,
                    ActionConstants::KEY_TITLE => 'Luar Biasa!',
                    ActionConstants::KEY_MESSAGE => 'Kamu sangat cepat! Mari coba tantangan yang lebih sulit.',
                    StudentStateSchema::TARGET_DIFFICULTY => 'next',
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(PedagogicalConstants::XP_REWARD_MEDIUM),
                    StudentStateSchema::CURRENT_STREAK => ActionConstants::inc(1),
                    StudentStateSchema::TOTAL_QUESTIONS_ANSWERED => ActionConstants::inc(1)
                ]
            ],
            [
                'code' => ActionConstants::REDUCE_DIFF,
                'variant' => 'result',
                'name' => ActionConstants::NAMES[ActionConstants::REDUCE_DIFF],
                'description' => 'Turunkan tingkat kesulitan soal.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_DOWN,
                    ActionConstants::KEY_TITLE => 'Jangan Menyerah',
                    ActionConstants::KEY_MESSAGE => 'Materi ini mungkin agak sulit. Mari kita coba dari dasar lagi.',
                    StudentStateSchema::TARGET_DIFFICULTY => 'prev',
                    StudentStateSchema::CURRENT_STREAK => '0',
                    StudentStateSchema::TOTAL_QUESTIONS_ANSWERED => ActionConstants::inc(1)
                ]
            ],
            [
                'code' => ActionConstants::STUDY_MATERIAL,
                'variant' => 'result',
                'name' => ActionConstants::NAMES[ActionConstants::STUDY_MATERIAL],
                'description' => 'Mengarahkan mahasiswa kembali ke materi.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_REVIEW,
                    ActionConstants::KEY_TITLE => 'Saran Belajar',
                    ActionConstants::KEY_MESSAGE => 'Kamu sepertinya butuh mereview materi ini kembali sebelum lanjut.',
                    StudentStateSchema::CURRENT_STREAK => '0',
                ]
            ],
            [
                'code' => ActionConstants::WRONG_ANSWER,
                'variant' => 'result',
                'name' => ActionConstants::NAMES[ActionConstants::WRONG_ANSWER],
                'description' => 'Notifikasi jawaban salah.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NEXT,
                    ActionConstants::KEY_TITLE => 'Belum Tepat',
                    ActionConstants::KEY_MESSAGE => 'Jawabanmu belum benar. Mari coba soal lain.',
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(0),
                    StudentStateSchema::CURRENT_STREAK => '0',
                    StudentStateSchema::WRONG_COUNT => ActionConstants::inc(1),
                    StudentStateSchema::TOTAL_QUESTIONS_ANSWERED => ActionConstants::inc(1)
                ]
            ],
            [
                'code' => ActionConstants::AWARD_BADGE,
                'variant' => 'gamification',
                'name' => ActionConstants::NAMES[ActionConstants::AWARD_BADGE],
                'description' => 'Memberikan badge penghargaan.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NONE,
                    ActionConstants::KEY_TITLE => 'Pencapaian Baru!',
                    ActionConstants::KEY_MESSAGE => 'Kamu mendapatkan badge baru atas performamu!',
                    ActionConstants::KEY_BADGES => ['Fast Learner'],
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(50)
                ]
            ],
            [
                'code' => ActionConstants::CELEBRATION,
                'variant' => 'acceleration',
                'name' => ActionConstants::NAMES[ActionConstants::CELEBRATION],
                'description' => 'Perayaan kelulusan modul.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_FINISH,
                    ActionConstants::KEY_TITLE => 'Selamat! Kamu Lulus!',
                    ActionConstants::KEY_MESSAGE => 'Kamu telah menguasai materi ini dengan sangat baik.',
                    ActionConstants::KEY_CERTIFICATION => 'Gold',
                    ActionConstants::KEY_BADGES => ['Module Conqueror'],
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(500)
                ]
            ],
            [
                'code' => ActionConstants::STREAK_BONUS,
                'variant' => 'info',
                'name' => ActionConstants::NAMES[ActionConstants::STREAK_BONUS],
                'description' => 'Bonus XP untuk jawaban benar berturut-turut.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NONE,
                    ActionConstants::KEY_TITLE => 'Sedang Membara! 🔥',
                    ActionConstants::KEY_MESSAGE => 'Kamu menjawab dengan cepat dan tepat! Bonus XP diberikan.',
                    StudentStateSchema::GLOBAL_XP => ActionConstants::inc(25)
                ]
            ],
            [
                'code' => ActionConstants::EMPATHY_MSG,
                'variant' => 'warning',
                'name' => ActionConstants::NAMES[ActionConstants::EMPATHY_MSG],
                'description' => 'Pesan penyemangat saat mahasiswa kesulitan.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_DOWN,
                    ActionConstants::KEY_TITLE => 'Jangan Menyerah! 💪',
                    ActionConstants::KEY_MESSAGE => 'Materi ini mungkin terasa sulit, tapi kamu pasti bisa. Mari kita coba soal yang lebih mendasar.',
                ]
            ],
            [
                'code' => 'H10',
                'variant' => 'danger',
                'name' => 'Intervensi Krisis',
                'description' => 'Aksi darurat ketika mahasiswa gagal berkali-kali.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_REVIEW,
                    ActionConstants::KEY_TITLE => 'Sistem Mendeteksi Kesulitan Serius ⚠️',
                    ActionConstants::KEY_MESSAGE => 'Kamu telah mencoba berkali-kali namun belum berhasil. Kami menyarankanmu untuk beristirahat sejenak dan mempelajari video tutorial ini sebelum melanjutkan.',
                ]
            ],
            [
                'code' => 'H11',
                'variant' => 'info',
                'name' => 'Edukasi Bantuan',
                'description' => 'Mengingatkan mahasiswa agar tidak terlalu bergantung pada hint.',
                'instructions' => [
                    ActionConstants::KEY_FLOW => ActionConstants::FLOW_NEXT,
                    ActionConstants::KEY_TITLE => 'Tips Belajar 💡',
                    ActionConstants::KEY_MESSAGE => 'Kami melihat kamu sering menggunakan bantuan. Cobalah untuk mengerjakan soal berikutnya tanpa bantuan agar pemahamanmu lebih kuat!',
                ]
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
            // ... (keep existing R01-R12) ...
            [
                'rule_code' => 'R13',
                'name' => 'Emergency Crisis Intervention',
                'domain' => 'SafetyNet',
                'priority' => -1, // Prioritas sangat tinggi
                'required_facts' => [FactConstants::V_CRISIS_STATE],
                'action_id' => $actionIds['H10'],
            ],
            [
                'rule_code' => 'R14',
                'name' => 'Hint Dependency Warning',
                'domain' => 'Behaviour',
                'priority' => 4,
                'required_facts' => [FactConstants::V_HINT_DEPENDENT, FactConstants::SCORE_PASS],
                'action_id' => $actionIds['H11'],
            ],
            [
                'rule_code' => 'R15',
                'name' => 'Anti-Boredom Fast Track',
                'domain' => 'Progression',
                'priority' => 2,
                'required_facts' => [FactConstants::V_BOREDOM_DETECTED],
                'action_id' => $actionIds[ActionConstants::INCREASE_DIFF],
            ],
            // ─── Promotion Rules (Treatment: Accelerate) ────────────────────
            [
                'rule_code' => 'R01',
                'name' => 'Fast Track for Experts',
                'domain' => 'Progression',
                'priority' => 1,
                'required_facts' => [FactConstants::V_MASTERY_HARD, FactConstants::V_EXCELLENT_RESULT],
                'action_id' => $actionIds[ActionConstants::CELEBRATION],
            ],
            [
                'rule_code' => 'R03',
                'name' => 'Adaptive Difficulty Up',
                'domain' => 'Progression',
                'priority' => 3,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT],
                'action_id' => $actionIds[ActionConstants::INCREASE_DIFF],
            ],

            // ─── Intervention Rules (Treatment: Recovery) ───────────────────
            [
                'rule_code' => 'R07',
                'name' => 'Crisis Intervention',
                'domain' => 'SafetyNet',
                'priority' => 1,
                'required_facts' => [FactConstants::V_UNFOCUSED],
                'action_id' => $actionIds[ActionConstants::STUDY_MATERIAL],
            ],
            // ─── Mastery Rules (Treatment: Rewards) ─────────────────────────
            [
                'rule_code' => 'R06',
                'name' => 'Steady Progress Path',
                'domain' => 'Progression',
                'priority' => 5,
                'required_facts' => [FactConstants::V_STEADY_LEARNER],
                'action_id' => $actionIds[ActionConstants::NEXT_QUESTION],
            ],
            [
                'rule_code' => 'R10',
                'name' => 'Medium Mastery Achievement',
                'domain' => 'Mastery',
                'priority' => 2,
                'required_facts' => [FactConstants::V_MASTERY_MEDIUM],
                'action_id' => $actionIds[ActionConstants::AWARD_BADGE],
            ],
            [
                'rule_code' => 'R04',
                'name' => 'Beginner Mastery Milestone',
                'domain' => 'Mastery',
                'priority' => 5,
                'required_facts' => [FactConstants::V_MASTERY_BEGINNER],
                'action_id' => $actionIds[ActionConstants::AWARD_BADGE],
            ],
            [
                'rule_code' => 'R11',
                'name' => 'Streak Fire Bonus',
                'domain' => 'Gamification',
                'priority' => 0,
                'required_facts' => [FactConstants::V_EXCELLENT_RESULT],
                'action_id' => $actionIds[ActionConstants::STREAK_BONUS],
            ],
            [
                'rule_code' => 'R12',
                'name' => 'Empathy Intervention',
                'domain' => 'SafetyNet',
                'priority' => 0,
                'required_facts' => [FactConstants::V_STRUGGLING],
                'action_id' => $actionIds[ActionConstants::EMPATHY_MSG],
            ],

            // ─── Direct Behavior Rules (No Virtual Fact Needed) ─────────────
            [
                'rule_code' => 'R16',
                'name' => 'Flash Success',
                'domain' => 'Progression',
                'priority' => 15, // Low priority fallback
                'required_facts' => [FactConstants::SCORE_PASS, FactConstants::TIME_QUICK],
                'action_id' => $actionIds[ActionConstants::NEXT_QUESTION],
            ],
            [
                'rule_code' => 'R17',
                'name' => 'Persistent Struggle',
                'domain' => 'Recovery',
                'priority' => 4, 
                'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::HINT_USED],
                'action_id' => $actionIds[ActionConstants::REDUCE_DIFF],
            ],
            [
                'rule_code' => 'R18',
                'name' => 'Mastery Reward',
                'domain' => 'Mastery',
                'priority' => 8,
                'required_facts' => [FactConstants::V_MASTERY_HARD, FactConstants::SCORE_PASS],
                'action_id' => $actionIds[ActionConstants::AWARD_BADGE],
            ],
            [
                'rule_code' => 'R19',
                'name' => 'Lucky Beginner Jump',
                'domain' => 'Progression',
                'priority' => 3,
                'required_facts' => [FactConstants::V_MASTERY_BEGINNER, FactConstants::TIME_QUICK, FactConstants::SCORE_PASS],
                'action_id' => $actionIds[ActionConstants::INCREASE_DIFF],
            ],

            // ── Deduction Rules (Internal Logic) ──────────────────────────
            [
                'rule_code' => 'D01',
                'name' => 'Deduce Excellent Result',
                'domain' => 'Deduction',
                'priority' => 0,
                'required_facts' => [FactConstants::SCORE_PASS, FactConstants::TIME_QUICK],
                'deduced_facts' => [FactConstants::V_EXCELLENT_RESULT],
                'action_id' => null,
            ],
            [
                'rule_code' => 'D02',
                'name' => 'Deduce Struggle',
                'domain' => 'Deduction',
                'priority' => 0,
                'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::TIME_SLOW],
                'deduced_facts' => [FactConstants::V_STRUGGLING],
                'action_id' => null,
            ],
            [
                'rule_code' => 'D03',
                'name' => 'Deduce Steady Learner',
                'domain' => 'Deduction',
                'priority' => 0,
                'required_facts' => [FactConstants::SCORE_PASS, FactConstants::TIME_SLOW],
                'deduced_facts' => [FactConstants::V_STEADY_LEARNER],
                'action_id' => null,
            ],
            [
                'rule_code' => 'D04',
                'name' => 'Deduce Unfocused',
                'domain' => 'Deduction',
                'priority' => 0,
                'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::TIME_QUICK],
                'deduced_facts' => [FactConstants::V_UNFOCUSED],
                'action_id' => null,
            ],
            [
                'rule_code' => 'D05',
                'name' => 'Deduce Hint Struggle',
                'domain' => 'Deduction',
                'priority' => 0,
                'required_facts' => [FactConstants::SCORE_FAIL, FactConstants::HINT_USED],
                'deduced_facts' => [FactConstants::V_STRUGGLING],
                'action_id' => null,
            ],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create($rule + ['is_active' => true]);
        }

        \Illuminate\Support\Facades\Cache::forget('adaptive_rules_all');
        \Illuminate\Support\Facades\Cache::forget('adaptive_rules_count');
    }
}
