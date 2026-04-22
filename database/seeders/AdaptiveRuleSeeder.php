<?php

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdaptiveRuleSeeder extends Seeder
{
    private array $factIds = [];

    private array $actionIds = [];

    public function run(): void
    {
        $this->clearExisting();

        DB::transaction(function () {
            $this->seedFacts();
            $this->seedActions();
            $this->seedRules();
        });
    }

    private function clearExisting(): void
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
            // Skor & Performa (G01-G08)
            ['code' => 'G01', 'category' => 'performance', 'name' => AC::FACT_SCORE_FAILURE, 'description' => 'Skor rendah (<70).'],
            ['code' => 'G02', 'category' => 'performance', 'name' => AC::FACT_SCORE_PASS,    'description' => 'Skor cukup (70-89).'],
            ['code' => 'G03', 'category' => 'performance', 'name' => AC::FACT_SCORE_PERFECT, 'description' => 'Skor sempurna (90+).'],
            ['code' => 'G04', 'category' => 'performance', 'name' => AC::FACT_SCORE_ZERO,    'description' => 'Salah total (0).'],
            ['code' => 'G05', 'category' => 'performance', 'name' => AC::FACT_CONSISTENCY_HIGH, 'description' => 'Konsisten benar (streak).'],
            ['code' => 'G06', 'category' => 'performance', 'name' => AC::FACT_MASTERY_BEGINNER, 'description' => 'Kuasai level beginner.'],
            ['code' => 'G07', 'category' => 'performance', 'name' => AC::FACT_MASTERY_MEDIUM,   'description' => 'Kuasai level medium.'],
            ['code' => 'G08', 'category' => 'performance', 'name' => AC::FACT_MASTERY_HARD,     'description' => 'Kuasai level hard.'],

            // Gaya Belajar & Error (G09-G15)
            ['code' => 'G09', 'category' => 'style', 'name' => AC::FACT_STYLE_VISUAL,  'description' => 'Cenderung visual.'],
            ['code' => 'G10', 'category' => 'style', 'name' => AC::FACT_STYLE_TEXTUAL, 'description' => 'Cenderung tekstual.'],
            ['code' => 'G11', 'category' => 'style', 'name' => AC::FACT_STYLE_MIXED,   'description' => 'Gaya belajar campuran.'],
            ['code' => 'G12', 'category' => 'error', 'name' => AC::FACT_ERROR_SYNTAX,  'description' => 'Sering salah tulis.'],
            ['code' => 'G13', 'category' => 'error', 'name' => AC::FACT_ERROR_LOGIC,   'description' => 'Sering salah logika.'],
            ['code' => 'G14', 'category' => 'error', 'name' => AC::FACT_ERROR_CONCEPT, 'description' => 'Sering salah konsep.'],
            ['code' => 'G15', 'category' => 'error', 'name' => AC::FACT_NO_ERROR,      'description' => 'Tanpa kesalahan.'],

            // Waktu & Usaha (G16-G20)
            ['code' => 'G16', 'category' => 'time', 'name' => AC::FACT_TIME_FAST_SUCCESS, 'description' => 'Cepat & Benar.'],
            ['code' => 'G17', 'category' => 'time', 'name' => AC::FACT_TIME_FAST_FAIL,    'description' => 'Cepat & Salah (Ceroboh).'],
            ['code' => 'G18', 'category' => 'time', 'name' => AC::FACT_TIME_SLOW_SUCCESS, 'description' => 'Lambat & Benar.'],
            ['code' => 'G19', 'category' => 'time', 'name' => AC::FACT_TIME_SLOW_FAIL,    'description' => 'Lambat & Salah (Struggle).'],
            ['code' => 'G20', 'category' => 'time', 'name' => AC::FACT_HINT_USED,         'description' => 'Menggunakan hint.'],

            // Psikologis (G21-G25)
            ['code' => 'G21', 'category' => 'behaviour', 'name' => AC::FACT_BOREDOM_SIGNS, 'description' => 'Tanda kebosanan.'],
            ['code' => 'G22', 'category' => 'behaviour', 'name' => AC::FACT_ANXIETY_SIGNS, 'description' => 'Tanda kecemasan.'],
            ['code' => 'G23', 'category' => 'behaviour', 'name' => AC::FACT_HIGH_STRUGGLE, 'description' => 'Kesulitan tinggi.'],

            // Konteks & Progres (G26-G30)
            ['code' => 'G26', 'category' => 'difficulty', 'name' => AC::FACT_DIFF_BEGINNER, 'description' => 'Sedang di level beginner.'],
            ['code' => 'G27', 'category' => 'difficulty', 'name' => AC::FACT_IS_FINAL_PROJECT, 'description' => 'Sedang di Final Project.'],
            ['code' => 'G28', 'category' => 'progress', 'name' => AC::FACT_PERSISTENT_FAIL,  'description' => 'Gagal berturut-turut.'],
            ['code' => 'G29', 'category' => 'progress', 'name' => AC::FACT_MODULE_NEARLY_DONE, 'description' => 'Materi hampir selesai.'],
            ['code' => 'G30', 'category' => 'progress', 'name' => AC::FACT_MODULE_GRADUATION,  'description' => 'Layak lulus modul.'],
        ];

        foreach ($facts as $fact) {
            $created                      = AdaptiveFact::create($fact);
            $this->factIds[$fact['code']] = $created->id;
        }
    }

    private function seedActions(): void
    {
        $actions = [
            // ── Dasar (H01-H05)
            ['code' => 'H01', 'variant' => 'result',       'name' => 'Standard Promotion', 'description' => 'Lanjut normal.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Soal Berikutnya', 'title' => 'Luar Biasa!']],
            ['code' => 'H02', 'variant' => 'result',       'name' => 'Standard Remedial',  'description' => 'Ulang soal.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Coba Lagi', 'message' => 'Jawaban kurang tepat. Ayo coba lagi!', 'title' => 'Jangan Menyerah!']],
            ['code' => 'H03', 'variant' => 'acceleration', 'name' => 'Accelerated Jump',   'description' => 'Lompat level.', 'instructions' => ['target_difficulty' => 'hard', 'next_action' => AC::ACTION_INCREASE_DIFFICULTY, 'label' => 'Tantangan Baru', 'message' => 'Luar Biasa! Kamu menjawab dengan sangat cepat and tepat. Tantangan selanjutnya telah menantimu di level yang lebih tinggi!', 'title' => 'Percepatan Aktif!']],
            ['code' => 'H04', 'variant' => 'backtrack',    'name' => 'Critical Backtrack', 'description' => 'Turun level.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'label' => 'Bimbingan Level', 'message' => 'Sepertinya soal ini agak sulit. Kami menyesuaikan tingkat kesulitannya agar kamu lebih nyaman belajar!', 'title' => 'Penyesuaian Alur']],
            ['code' => 'H05', 'variant' => 'certificate',  'name' => 'Module Graduation',  'description' => 'Lulus modul.', 'instructions' => ['next_action' => AC::ACTION_FINISH_MATERIAL, 'label' => 'Selesaikan Modul', 'message' => 'Selamat! Kamu telah menyelesaikan modul ini.', 'title' => 'Kelulusan Modul Tercapai!']],

            // ── Intervensi Gaya Belajar (H06-H10)
            ['code' => 'H06', 'variant' => 'intervention', 'name' => 'Study Visual',       'description' => 'Paksa visual.', 'instructions' => ['next_action' => AC::ACTION_STUDY_VISUAL, 'label' => 'Materi Visual', 'title' => 'Bantuan Adaptif Aktif']],
            ['code' => 'H07', 'variant' => 'intervention', 'name' => 'Study Textual',      'description' => 'Paksa teks.', 'instructions' => ['next_action' => AC::ACTION_STUDY_TEXTUAL, 'label' => 'Materi Tekstual', 'title' => 'Bantuan Adaptif Aktif']],
            ['code' => 'H10', 'variant' => 'intervention', 'name' => 'Logic Guide',        'description' => 'Panduan alur.', 'instructions' => ['next_action' => AC::ACTION_STUDY_THEORY, 'label' => 'Pahami Konsep', 'title' => 'Bantuan Adaptif Aktif']],
            ['code' => 'H11', 'variant' => 'intervention', 'name' => 'Syntax Guide',       'description' => 'Panduan tulis.', 'instructions' => ['next_action' => AC::ACTION_STUDY_SYNTAX, 'label' => 'Pelajari Sintaks', 'title' => 'Bantuan Adaptif Aktif']],

            // ── Proyek & Sertifikat (H12-H16)
            ['code' => 'H12', 'variant' => 'intervention', 'name' => 'Project Review',     'description' => 'Review materi.', 'instructions' => ['next_action' => AC::ACTION_STUDY_MATERIAL, 'label' => 'Ulas Materi', 'title' => 'Ulasan Proyek']],
            ['code' => 'H13', 'variant' => 'intervention', 'name' => 'Project Revision',   'description' => 'Revisi proyek.', 'instructions' => ['next_action' => AC::ACTION_REVISE_PROJECT, 'label' => 'Revisi Proyek', 'title' => 'Revisi Proyek']],
            ['code' => 'H14', 'variant' => 'certificate',  'name' => 'Gold Medal',         'description' => 'Emas.', 'instructions' => ['award' => 'gold_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE, 'label' => 'Klaim Sertifikat Emas', 'message' => 'Sempurna! Kamu meraih Sertifikat Emas!', 'title' => 'Sertifikat Tercapai!']],
            ['code' => 'H15', 'variant' => 'certificate',  'name' => 'Silver Medal',       'description' => 'Perak.', 'instructions' => ['award' => 'silver_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE, 'label' => 'Klaim Sertifikat Perak', 'message' => 'Hebat! Kamu meraih Sertifikat Perak!', 'title' => 'Sertifikat Tercapai!']],
            ['code' => 'H16', 'variant' => 'certificate',  'name' => 'Bronze Medal',       'description' => 'Perunggu.', 'instructions' => ['award' => 'bronze_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE, 'label' => 'Klaim Sertifikat Perunggu', 'message' => 'Bagus! Kamu meraih Sertifikat Perunggu.', 'title' => 'Sertifikat Tercapai!']],

            // ── Psikologis & Motivasi (H17-H20)
            ['code' => 'H17', 'variant' => 'backtrack',    'name' => 'Anxiety Relief',     'description' => 'Turunkan beban.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'label' => 'Mulai Santai', 'message' => 'Rileks, mari kita pelan-pelan. Kamu pasti bisa!', 'title' => 'Penyesuaian Alur']],
            ['code' => 'H18', 'variant' => 'acceleration', 'name' => 'Challenge Mode',      'description' => 'Beri tantangan.', 'instructions' => ['target_difficulty' => 'hard', 'next_action' => AC::ACTION_INCREASE_DIFFICULTY, 'label' => 'Mode Tantangan', 'message' => 'Sepertinya ini terlalu mudah bagimu! Ayo naik level.', 'title' => 'Percepatan Aktif!']],
            ['code' => 'H19', 'variant' => 'result',       'name' => 'Motivational Msg',   'description' => 'Pesan semangat.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Soal Berikutnya', 'message' => 'Pantang menyerah! Sikit lagi benar.', 'title' => 'Tetap Semangat!']],
            ['code' => 'H20', 'variant' => 'result',       'name' => 'Careful Alert',      'description' => 'Peringatan ceroboh.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Soal Berikutnya', 'message' => 'Jangan terburu-buru, baca lagi teliti.', 'title' => 'Hati-hati!']],
            ['code' => 'H21', 'variant' => 'intervention', 'name' => 'Study Mixed',        'description' => 'Materi Komprehensif.', 'instructions' => ['next_action' => AC::ACTION_STUDY_MIXED, 'label' => 'Materi Komprehensif', 'title' => 'Bantuan Adaptif Aktif']],

            // ── Intervensi Krisis (H22-H23) — Baru
            ['code' => 'H22', 'variant' => 'intervention', 'name' => 'Crisis Intervention', 'description' => 'Intervensi skor nol.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_STUDY_MATERIAL, 'label' => 'Pelajari Materi Dulu', 'message' => 'Sepertinya kamu perlu mempelajari materinya terlebih dahulu. Ayo kita mulai dari dasar!', 'title' => 'Bantuan Adaptif Aktif']],
            ['code' => 'H23', 'variant' => 'backtrack',    'name' => 'Persistent Fail Aid', 'description' => 'Bantuan gagal berturut.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'label' => 'Bimbingan Khusus', 'message' => 'Kamu sudah berusaha keras. Mari kita coba pendekatan yang berbeda dengan soal yang lebih mudah.', 'title' => 'Bantuan Adaptif Aktif']],
        ];

        foreach ($actions as $action) {
            $created                          = AdaptiveAction::create($action);
            $this->actionIds[$action['code']] = $created->id;
        }
    }

    private function seedRules(): void
    {
        $rules = [
            // ══════════════════════════════════════════════════════════════
            // TIER 1: Jalur Cepat / Bosan (priority 1-4)
            // ══════════════════════════════════════════════════════════════
            // R01: Perfect + Fast + Boredom → Naikkan difficulty (tantangan)
            ['code' => 'R01', 'name' => 'Boredom Challenge',  'priority' =>  1, 'required' => ['G03', 'G16', 'G21'], 'forbidden' => ['G24'], 'action' => 'H18'],
            // R02: Perfect + Fast (tanpa hint) → Lompat ke hard
            ['code' => 'R02', 'name' => 'Elite Jump',         'priority' =>  2, 'required' => ['G03', 'G16'],        'forbidden' => ['G20'], 'action' => 'H03'],

            // ══════════════════════════════════════════════════════════════
            // TIER 2: Proyek & Sertifikat (priority 5-10)
            // ══════════════════════════════════════════════════════════════
            // R03: Final Project + Perfect + Graduation + Mastery Hard → Gold
            ['code' => 'R03', 'name' => 'Gold Award',         'priority' =>  5, 'required' => ['G27', 'G03', 'G30'], 'forbidden' => ['G20'], 'action' => 'H14'],
            // R04: Final Project + Pass + Graduation → Silver
            ['code' => 'R04', 'name' => 'Silver Award',       'priority' =>  6, 'required' => ['G27', 'G02', 'G30'], 'forbidden' => ['G20'], 'action' => 'H15'],
            // R05: Final Project + Pass + Satisfactory Progress → Bronze
            ['code' => 'R05', 'name' => 'Bronze Award',       'priority' =>  7, 'required' => ['G27', 'G02', 'G32'], 'forbidden' => null,    'action' => 'H16'],
            // R06: Final Project + Fail + Visual → Revisi visual
            ['code' => 'R06', 'name' => 'Project Visual Rev', 'priority' =>  8, 'required' => ['G27', 'G01', 'G09'], 'forbidden' => null,    'action' => 'H13'],
            // R07: Final Project + Fail + Textual → Revisi tekstual
            ['code' => 'R07', 'name' => 'Project Text Rev',   'priority' =>  9, 'required' => ['G27', 'G01', 'G10'], 'forbidden' => null,    'action' => 'H13'],
            // R08: Final Project + Fail (fallback) → Review materi
            ['code' => 'R08', 'name' => 'Project Fallback',   'priority' => 10, 'required' => ['G27', 'G01'],        'forbidden' => null,    'action' => 'H12'],

            // ══════════════════════════════════════════════════════════════
            // TIER 3: Intervensi Krisis (priority 12-17)
            // ══════════════════════════════════════════════════════════════
            // R19: Score Zero → Intervensi krisis, arahkan ke materi
            ['code' => 'R19', 'name' => 'Zero Score Crisis',  'priority' => 12, 'required' => ['G04'],               'forbidden' => null,    'action' => 'H22'],
            // R09: Fail + Anxiety + Slow Fail → Turunkan beban
            ['code' => 'R09', 'name' => 'Anxiety Safety Net', 'priority' => 13, 'required' => ['G01', 'G22'],        'forbidden' => null,    'action' => 'H17'],
            // R10: Persistent Fail → Backtrack ke beginner
            ['code' => 'R10', 'name' => 'Persistent Struggle', 'priority' => 14, 'required' => ['G28', 'G01'],       'forbidden' => null,    'action' => 'H23'],
            // R20: High Struggle → Backtrack ke beginner
            ['code' => 'R20', 'name' => 'High Struggle Aid',  'priority' => 15, 'required' => ['G23', 'G01'],        'forbidden' => null,    'action' => 'H04'],
            // R11: Fail + Fast Fail → Peringatan ceroboh
            ['code' => 'R11', 'name' => 'Careless Failure',   'priority' => 17, 'required' => ['G01', 'G17'],        'forbidden' => null,    'action' => 'H20'],

            // ══════════════════════════════════════════════════════════════
            // TIER 4: Error-Specific Remediation (priority 18-19)
            // Prioritas lebih tinggi dari gaya belajar generik
            // ══════════════════════════════════════════════════════════════
            // R14: Syntax Error + Fail → Panduan sintaks
            ['code' => 'R14', 'name' => 'Syntax Error Help',  'priority' => 18, 'required' => ['G12', 'G01'],        'forbidden' => null,    'action' => 'H11'],
            // R15: Logic Error + Fail → Panduan logika
            ['code' => 'R15', 'name' => 'Logic Error Help',   'priority' => 19, 'required' => ['G13', 'G01'],        'forbidden' => null,    'action' => 'H10'],

            // ══════════════════════════════════════════════════════════════
            // TIER 5: Gaya Belajar Generik (priority 20-24)
            // ══════════════════════════════════════════════════════════════
            // R12: Fail + Visual → Materi visual
            ['code' => 'R12', 'name' => 'Visual Preference',  'priority' => 20, 'required' => ['G01', 'G09'],        'forbidden' => ['G12', 'G13'], 'action' => 'H06'],
            // R13: Fail + Textual → Materi tekstual
            ['code' => 'R13', 'name' => 'Textual Preference', 'priority' => 21, 'required' => ['G01', 'G10'],        'forbidden' => ['G12', 'G13'], 'action' => 'H07'],
            // R18: Fail + Mixed → Materi komprehensif
            ['code' => 'R18', 'name' => 'Mixed Preference',   'priority' => 22, 'required' => ['G01', 'G11'],        'forbidden' => ['G12', 'G13'], 'action' => 'H21'],

            // ══════════════════════════════════════════════════════════════
            // TIER 6: Graduation & Fallback (priority 25-35)
            // ══════════════════════════════════════════════════════════════
            // R16: Module Graduation → Selesaikan modul
            ['code' => 'R16', 'name' => 'Graduation Check',   'priority' => 25, 'required' => ['G30', 'G32'],        'forbidden' => null,    'action' => 'H05'],
            // R21: Slow Success → Pesan motivasi (hati-hati tapi benar)
            ['code' => 'R21', 'name' => 'Slow But Steady',    'priority' => 28, 'required' => ['G02', 'G18'],        'forbidden' => null,    'action' => 'H19'],
            // R17: Pass (fallback) → Lanjut normal
            ['code' => 'R17', 'name' => 'Default Pass',       'priority' => 30, 'required' => ['G02'],               'forbidden' => null,    'action' => 'H01'],
            // R22: Fail generik (fallback terakhir sebelum engine fallback)
            ['code' => 'R22', 'name' => 'Default Remedial',   'priority' => 35, 'required' => ['G01'],               'forbidden' => null,    'action' => 'H02'],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create([
                'rule_code'       => $rule['code'],
                'name'            => $rule['name'],
                'priority'        => $rule['priority'],
                'required_facts'  => $rule['required'],
                'forbidden_facts' => $rule['forbidden'],
                'action_id'       => $this->actionIds[$rule['action']],
                'is_active'       => true,
            ]);
        }
    }
}
