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
        DB::transaction(function () {
            $this->clearExisting();
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
            ['code' => 'G01', 'name' => AC::FACT_SCORE_FAILURE, 'description' => 'Skor rendah (<70).'],
            ['code' => 'G02', 'name' => AC::FACT_SCORE_PASS,    'description' => 'Skor cukup (70-89).'],
            ['code' => 'G03', 'name' => AC::FACT_SCORE_PERFECT, 'description' => 'Skor sempurna (90+).'],
            ['code' => 'G04', 'name' => AC::FACT_SCORE_ZERO,    'description' => 'Salah total (0).'],
            ['code' => 'G05', 'name' => AC::FACT_CONSISTENCY_HIGH, 'description' => 'Konsisten benar (streak).'],
            ['code' => 'G06', 'name' => AC::FACT_MASTERY_BEGINNER, 'description' => 'Kuasai level beginner.'],
            ['code' => 'G07', 'name' => AC::FACT_MASTERY_MEDIUM,   'description' => 'Kuasai level medium.'],
            ['code' => 'G08', 'name' => AC::FACT_MASTERY_HARD,     'description' => 'Kuasai level hard.'],

            // Gaya Belajar & Error (G09-G15)
            ['code' => 'G09', 'name' => AC::FACT_STYLE_VISUAL,  'description' => 'Cenderung visual.'],
            ['code' => 'G10', 'name' => AC::FACT_STYLE_TEXTUAL, 'description' => 'Cenderung tekstual.'],
            ['code' => 'G11', 'name' => AC::FACT_STYLE_MIXED,   'description' => 'Gaya belajar campuran.'],
            ['code' => 'G12', 'name' => AC::FACT_ERROR_SYNTAX,  'description' => 'Sering salah tulis.'],
            ['code' => 'G13', 'name' => AC::FACT_ERROR_LOGIC,   'description' => 'Sering salah logika.'],
            ['code' => 'G14', 'name' => AC::FACT_ERROR_CONCEPT, 'description' => 'Sering salah konsep.'],
            ['code' => 'G15', 'name' => AC::FACT_NO_ERROR,      'description' => 'Tanpa kesalahan.'],

            // Waktu & Usaha (G16-G20)
            ['code' => 'G16', 'name' => AC::FACT_TIME_FAST_SUCCESS, 'description' => 'Cepat & Benar.'],
            ['code' => 'G17', 'name' => AC::FACT_TIME_FAST_FAIL,    'description' => 'Cepat & Salah (Ceroboh).'],
            ['code' => 'G18', 'name' => AC::FACT_TIME_SLOW_SUCCESS, 'description' => 'Lambat & Benar.'],
            ['code' => 'G19', 'name' => AC::FACT_TIME_SLOW_FAIL,    'description' => 'Lambat & Salah (Struggle).'],
            ['code' => 'G20', 'name' => AC::FACT_HINT_USED,         'description' => 'Menggunakan hint.'],

            // Psikologis (G21-G25)
            ['code' => 'G21', 'name' => AC::FACT_BOREDOM_SIGNS, 'description' => 'Tanda kebosanan.'],
            ['code' => 'G22', 'name' => AC::FACT_ANXIETY_SIGNS, 'description' => 'Tanda kecemasan.'],
            ['code' => 'G23', 'name' => AC::FACT_HIGH_STRUGGLE, 'description' => 'Kesulitan tinggi.'],

            // Konteks & Progres (G26-G30)
            ['code' => 'G26', 'name' => AC::FACT_DIFF_BEGINNER, 'description' => 'Sedang di level beginner.'],
            ['code' => 'G27', 'name' => AC::FACT_IS_FINAL_PROJECT, 'description' => 'Sedang di Final Project.'],
            ['code' => 'G28', 'name' => AC::FACT_PERSISTENT_FAIL,  'description' => 'Gagal berturut-turut.'],
            ['code' => 'G29', 'name' => AC::FACT_MODULE_NEARLY_DONE, 'description' => 'Materi hampir selesai.'],
            ['code' => 'G30', 'name' => AC::FACT_MODULE_GRADUATION,  'description' => 'Layak lulus modul.'],
        ];

        foreach ($facts as $fact) {
            $created = AdaptiveFact::create($fact);
            $this->factIds[$fact['code']] = $created->id;
        }
    }

    private function seedActions(): void
    {
        $actions = [
            // ── Dasar (H01-H05)
            ['code' => 'H01', 'name' => 'Standard Promotion', 'description' => 'Lanjut normal.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION]],
            ['code' => 'H02', 'name' => 'Standard Remedial',  'description' => 'Ulang soal.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION]],
            ['code' => 'H03', 'name' => 'Accelerated Jump',   'description' => 'Lompat level.', 'instructions' => ['target_difficulty' => 'hard', 'next_action' => AC::ACTION_INCREASE_DIFFICULTY]],
            ['code' => 'H04', 'name' => 'Critical Backtrack', 'description' => 'Turun level.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY]],
            ['code' => 'H05', 'name' => 'Module Graduation',  'description' => 'Lulus modul.', 'instructions' => ['next_action' => AC::ACTION_FINISH_MATERIAL]],

            // ── Intervensi Gaya Belajar (H06-H11)
            ['code' => 'H06', 'name' => 'Study Visual',       'description' => 'Paksa visual.', 'instructions' => ['next_action' => AC::ACTION_STUDY_VISUAL]],
            ['code' => 'H07', 'name' => 'Study Textual',      'description' => 'Paksa teks.', 'instructions' => ['next_action' => AC::ACTION_STUDY_TEXTUAL]],
            ['code' => 'H08', 'name' => 'Visual Hint',        'description' => 'Beri diagram.', 'instructions' => ['next_action' => AC::ACTION_SHOW_VISUAL_HINT]],
            ['code' => 'H09', 'name' => 'Textual Hint',       'description' => 'Beri teks.', 'instructions' => ['next_action' => AC::ACTION_SHOW_TEXT_HINT]],
            ['code' => 'H10', 'name' => 'Logic Guide',        'description' => 'Panduan alur.', 'instructions' => ['next_action' => AC::ACTION_STUDY_THEORY]],
            ['code' => 'H11', 'name' => 'Syntax Guide',       'description' => 'Panduan tulis.', 'instructions' => ['next_action' => AC::ACTION_STUDY_SYNTAX]],

            // ── Proyek & Sertifikat (H12-H16)
            ['code' => 'H12', 'name' => 'Project Review',     'description' => 'Review materi.', 'instructions' => ['next_action' => 'STUDY_MATERIAL']],
            ['code' => 'H13', 'name' => 'Project Revision',   'description' => 'Revisi proyek.', 'instructions' => ['next_action' => AC::ACTION_REVISE_PROJECT]],
            ['code' => 'H14', 'name' => 'Gold Medal',         'description' => 'Emas.', 'instructions' => ['award' => 'gold_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE]],
            ['code' => 'H15', 'name' => 'Silver Medal',       'description' => 'Perak.', 'instructions' => ['award' => 'silver_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE]],
            ['code' => 'H16', 'name' => 'Bronze Medal',       'description' => 'Perunggu.', 'instructions' => ['award' => 'bronze_cert', 'next_action' => AC::ACTION_ISSUE_CERTIFICATE]],

            // ── Psikologis & Motivasi (H17-H20)
            ['code' => 'H17', 'name' => 'Anxiety Relief',     'description' => 'Turunkan beban.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'message' => 'Rileks, mari kita pelan-pelan.']],
            ['code' => 'H18', 'name' => 'Challenge Mode',      'description' => 'Beri tantangan.', 'instructions' => ['target_difficulty' => 'hard', 'next_action' => AC::ACTION_INCREASE_DIFFICULTY, 'message' => 'Sepertinya ini terlalu mudah bagimu!']],
            ['code' => 'H19', 'name' => 'Motivational Msg',   'description' => 'Pesan semangat.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'message' => 'Pantang menyerah! Sikit lagi benar.']],
            ['code' => 'H20', 'name' => 'Careful Alert',      'description' => 'Peringatan ceroboh.', 'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'message' => 'Jangan terburu-buru, baca lagi teliti.']],
        ];

        foreach ($actions as $action) {
            $created = AdaptiveAction::create($action);
            $this->actionIds[$action['code']] = $created->id;
        }
    }

    private function seedRules(): void
    {
        $rules = [
            // ── Jalur Cepat / Bosan (1-2)
            ['code' => 'R01', 'name' => 'Boredom Challenge',  'priority' =>  1, 'required' => ['G03', 'G16', 'G21'], 'forbidden' => ['G26'], 'action' => 'H18'],
            ['code' => 'R02', 'name' => 'Elite Jump',         'priority' =>  2, 'required' => ['G03', 'G16'],        'forbidden' => ['G20'], 'action' => 'H03'],
            
            // ── Proyek & Sertifikat (3-8)
            ['code' => 'R03', 'name' => 'Gold Award',         'priority' =>  5, 'required' => ['G27', 'G03', 'G30', 'G08'], 'forbidden' => ['G20'], 'action' => 'H14'],
            ['code' => 'R04', 'name' => 'Silver Award',       'priority' =>  6, 'required' => ['G27', 'G02', 'G30'],        'forbidden' => ['G20'], 'action' => 'H15'],
            ['code' => 'R05', 'name' => 'Bronze Award',       'priority' =>  7, 'required' => ['G27', 'G02'],               'forbidden' => null,    'action' => 'H16'],
            ['code' => 'R06', 'name' => 'Project Visual Rev', 'priority' =>  8, 'required' => ['G27', 'G01', 'G09'],        'forbidden' => null,    'action' => 'H13'],
            ['code' => 'R07', 'name' => 'Project Text Rev',   'priority' =>  9, 'required' => ['G27', 'G01', 'G10'],        'forbidden' => null,    'action' => 'H13'],
            ['code' => 'R08', 'name' => 'Project Fallback',   'priority' => 10, 'required' => ['G27', 'G01'],               'forbidden' => null,    'action' => 'H12'],

            // ── Cemas & Frustrasi (9-11)
            ['code' => 'R09', 'name' => 'Anxiety Safety Net', 'priority' => 15, 'required' => ['G01', 'G22', 'G19'],        'forbidden' => null,    'action' => 'H17'],
            ['code' => 'R10', 'name' => 'Persistent Struggle','priority' => 16, 'required' => ['G28', 'G23'],               'forbidden' => null,    'action' => 'H04'],
            ['code' => 'R11', 'name' => 'Careless Failure',   'priority' => 17, 'required' => ['G01', 'G17'],               'forbidden' => null,    'action' => 'H20'],

            // ── Gaya Belajar & Hint (12-15)
            ['code' => 'R12', 'name' => 'Visual Preference',  'priority' => 20, 'required' => ['G01', 'G09'],               'forbidden' => null,    'action' => 'H06'],
            ['code' => 'R13', 'name' => 'Textual Preference', 'priority' => 21, 'required' => ['G01', 'G10'],               'forbidden' => null,    'action' => 'H07'],
            ['code' => 'R14', 'name' => 'Syntax Error Help',  'priority' => 22, 'required' => ['G12', 'G01'],               'forbidden' => null,    'action' => 'H11'],
            ['code' => 'R15', 'name' => 'Logic Error Help',   'priority' => 23, 'required' => ['G13', 'G01'],               'forbidden' => null,    'action' => 'H10'],

            // ── Graduation & Fallback (16-17)
            ['code' => 'R16', 'name' => 'Graduation Check',   'priority' => 25, 'required' => ['G30'],                      'forbidden' => null,    'action' => 'H05'],
            ['code' => 'R17', 'name' => 'Default Pass',       'priority' => 30, 'required' => ['G02'],                      'forbidden' => null,    'action' => 'H01'],
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
