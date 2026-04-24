<?php

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Rules\Adaptive\FactRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Adaptive Rule Seeder – Pure Forward Chaining (Detective Model).
 *
 * Filosofi:
 *  - TIDAK ada forbidden_facts. Semua kondisi direpresentasikan sebagai fakta positif.
 *  - Beberapa aturan menghasilkan deduced_facts (Virtual Facts) untuk memicu aturan lain.
 *  - Mendukung Sertifikat (Bronze, Silver, Gold) dan Achievement Badges via Action Instructions.
 */
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
            // ── Performa Skor (G01-G04)
            ['code' => 'G01', 'category' => 'performance', 'name' => AC::FACT_SCORE_FAILURE,  'description' => 'Skor rendah (<70).'],
            ['code' => 'G02', 'category' => 'performance', 'name' => AC::FACT_SCORE_PASS,     'description' => 'Skor cukup (70-89).'],
            ['code' => 'G03', 'category' => 'performance', 'name' => AC::FACT_SCORE_PERFECT,  'description' => 'Skor sempurna (90+).'],
            ['code' => 'G04', 'category' => 'performance', 'name' => AC::FACT_SCORE_ZERO,     'description' => 'Salah total (0).'],

            // ── Konsistensi & Penguasaan (G05-G08)
            ['code' => 'G05', 'category' => 'performance', 'name' => AC::FACT_CONSISTENCY_HIGH,  'description' => 'Konsisten benar (streak ≥3).'],
            ['code' => 'G06', 'category' => 'performance', 'name' => AC::FACT_MASTERY_BEGINNER,  'description' => 'Kuasai level beginner.'],
            ['code' => 'G07', 'category' => 'performance', 'name' => AC::FACT_MASTERY_MEDIUM,    'description' => 'Kuasai level medium.'],
            ['code' => 'G08', 'category' => 'performance', 'name' => AC::FACT_MASTERY_HARD,      'description' => 'Kuasai level hard.'],

            // ── Gaya Belajar (G09-G11)
            ['code' => 'G09', 'category' => 'style', 'name' => AC::FACT_STYLE_VISUAL,   'description' => 'Cenderung visual.'],
            ['code' => 'G10', 'category' => 'style', 'name' => AC::FACT_STYLE_TEXTUAL,  'description' => 'Cenderung tekstual.'],
            ['code' => 'G11', 'category' => 'style', 'name' => AC::FACT_STYLE_MIXED,    'description' => 'Gaya belajar campuran.'],

            // ── Tipe Error (G12-G15)
            ['code' => 'G12', 'category' => 'error', 'name' => AC::FACT_ERROR_SYNTAX,   'description' => 'Sering salah tulis (syntax).'],
            ['code' => 'G13', 'category' => 'error', 'name' => AC::FACT_ERROR_LOGIC,    'description' => 'Sering salah logika.'],
            ['code' => 'G14', 'category' => 'error', 'name' => AC::FACT_ERROR_CONCEPT,  'description' => 'Sering salah konsep.'],
            ['code' => 'G15', 'category' => 'error', 'name' => AC::FACT_NO_ERROR,       'description' => 'Tanpa kesalahan.'],

            // ── Waktu (G16-G19)
            ['code' => 'G16', 'category' => 'time', 'name' => AC::FACT_TIME_FAST_SUCCESS,  'description' => 'Cepat & Benar.'],
            ['code' => 'G17', 'category' => 'time', 'name' => AC::FACT_TIME_FAST_FAIL,     'description' => 'Cepat & Salah (Ceroboh).'],
            ['code' => 'G18', 'category' => 'time', 'name' => AC::FACT_TIME_SLOW_SUCCESS,  'description' => 'Lambat & Benar.'],
            ['code' => 'G19', 'category' => 'time', 'name' => AC::FACT_TIME_SLOW_FAIL,     'description' => 'Lambat & Salah (Struggle).'],

            // ── Kemandirian & Hint (G20, G37)
            ['code' => 'G20', 'category' => 'behaviour', 'name' => AC::FACT_HINT_USED,        'description' => 'Menggunakan hint.'],
            ['code' => 'G37', 'category' => 'behaviour', 'name' => AC::FACT_INDEPENDENT_WORK, 'description' => 'Bekerja tanpa hint.'],

            // ── Perilaku Psikologis (G21-G23)
            ['code' => 'G21', 'category' => 'behaviour', 'name' => AC::FACT_BOREDOM_SIGNS,  'description' => 'Tanda kebosanan.'],
            ['code' => 'G22', 'category' => 'behaviour', 'name' => AC::FACT_ANXIETY_SIGNS,   'description' => 'Tanda kecemasan.'],
            ['code' => 'G23', 'category' => 'behaviour', 'name' => AC::FACT_HIGH_STRUGGLE,   'description' => 'Kesulitan tinggi.'],

            // ── Tingkat Kesulitan Saat Ini (G26, G31, G32)
            ['code' => 'G26', 'category' => 'difficulty', 'name' => AC::FACT_DIFF_BEGINNER,  'description' => 'Sedang di level beginner.'],
            ['code' => 'G31', 'category' => 'difficulty', 'name' => AC::FACT_DIFF_MEDIUM,    'description' => 'Sedang di level medium.'],
            ['code' => 'G32', 'category' => 'difficulty', 'name' => AC::FACT_DIFF_HARD,      'description' => 'Sedang di level hard.'],

            // ── Progres & Modul (G28-G36)
            ['code' => 'G28', 'category' => 'progress', 'name' => AC::FACT_PERSISTENT_FAIL,       'description' => 'Gagal berturut-turut.'],
            ['code' => 'G29', 'category' => 'progress', 'name' => AC::FACT_MODULE_NEARLY_DONE,    'description' => 'Materi hampir selesai.'],
            ['code' => 'G30', 'category' => 'progress', 'name' => AC::FACT_MODULE_GRADUATION,     'description' => 'Layak lulus modul.'],
            ['code' => 'G33', 'category' => 'progress', 'name' => AC::FACT_IN_MODULE,             'description' => 'Sedang dalam modul pembelajaran.'],
            ['code' => 'G34', 'category' => 'progress', 'name' => AC::FACT_SATISFACTORY_PROGRESS, 'description' => 'Progres memadai.'],
            ['code' => 'G35', 'category' => 'progress', 'name' => AC::FACT_NEXT_UNLOCKED,         'description' => 'Modul berikutnya sudah terbuka.'],
            ['code' => 'G38', 'category' => 'progress', 'name' => AC::FACT_NEXT_LOCKED,           'description' => 'Modul berikutnya masih terkunci.'],
            ['code' => 'G36', 'category' => 'progress', 'name' => AC::FACT_PREV_UNLOCKED,         'description' => 'Modul sebelumnya sudah terbuka.'],

            // ── Virtual / Deduced Facts (V01-V07) – Dihasilkan oleh aturan lain, bukan observasi langsung
            ['code' => 'V01', 'category' => 'deduced', 'name' => 'High Performer',              'description' => 'Deduksi: siswa berkinerja tinggi dan mandiri.'],
            ['code' => 'V02', 'category' => 'deduced', 'name' => 'Needs Foundation',            'description' => 'Deduksi: siswa perlu penguatan dasar.'],
            ['code' => 'V03', 'category' => 'deduced', 'name' => 'In Crisis',                   'description' => 'Deduksi: siswa dalam kondisi krisis belajar.'],
            ['code' => 'V04', 'category' => 'deduced', 'name' => 'Style Mismatch Visual',       'description' => 'Deduksi: siswa visual gagal karena soal berbasis logika/teks.'],
            ['code' => 'V05', 'category' => 'deduced', 'name' => 'Style Mismatch Textual',      'description' => 'Deduksi: siswa tekstual gagal karena soal berbasis sintaks.'],
            ['code' => 'V06', 'category' => 'deduced', 'name' => 'Mastery Ready Intermediate',  'description' => 'Deduksi: siswa siap naik dari Beginner ke Medium.'],
            ['code' => 'V07', 'category' => 'deduced', 'name' => 'Mastery Ready Advanced',      'description' => 'Deduksi: siswa siap naik dari Medium ke Hard.'],
            ['code' => 'V08', 'category' => 'deduced', 'name' => 'Conceptual Gap Detected',     'description' => 'Deduksi: siswa memiliki celah pemahaman konsep fundamental.'],
            ['code' => 'V09', 'category' => 'deduced', 'name' => 'Careless Pattern',            'description' => 'Deduksi: siswa cenderung terburu-buru meski mampu.'],
            ['code' => 'V10', 'category' => 'deduced', 'name' => 'Struggle Pattern',             'description' => 'Deduksi: siswa menunjukkan pola kesulitan yang konsisten.'],
        ];

        foreach ($facts as $fact) {
            $created                      = AdaptiveFact::create($fact);
            $this->factIds[$fact['code']] = $created->id;
        }
    }

    private function seedActions(): void
    {
        $actions = [
            // ── H00: Silent – Khusus untuk rule Deduksi, tidak ada output UI
            // Mesin hanya menambah virtual facts ke working memory, lalu lanjut ke siklus berikutnya.
            ['code' => 'H00', 'variant' => 'silent', 'name' => 'Silent Deduction', 'description' => 'Tidak ada aksi UI. Hanya menghasilkan virtual facts.', 'instructions' => ['next_action' => AC::ACTION_SILENT]],

            // ── Navigasi Soal (H01-H02)
            ['code' => 'H01', 'variant' => 'result',       'name' => 'Standard Promotion',  'description' => 'Lanjut normal.',         'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION,      'label' => 'Soal Berikutnya', 'title' => 'Luar Biasa!']],
            ['code' => 'H02', 'variant' => 'result',       'name' => 'Standard Remedial',   'description' => 'Ulang soal.',            'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION,      'label' => 'Coba Lagi',       'message' => 'Jawaban kurang tepat. Ayo coba lagi!', 'title' => 'Jangan Menyerah!']],

            // ── Percepatan & Penurunan Difficulty (H03-H04)
            ['code' => 'H03', 'variant' => 'acceleration', 'name' => 'Accelerated Jump',    'description' => 'Lompat level.',          'instructions' => ['target_difficulty' => 'hard', 'next_action' => AC::ACTION_INCREASE_DIFFICULTY, 'label' => 'Tantangan Baru', 'message' => 'Luar Biasa! Tantangan level lebih tinggi menantimu!', 'title' => 'Percepatan Aktif!']],
            ['code' => 'H04', 'variant' => 'backtrack',    'name' => 'Critical Backtrack',  'description' => 'Turun level.',           'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'label' => 'Bimbingan Level', 'message' => 'Kami menyesuaikan tingkat untuk kenyamanan belajarmu.', 'title' => 'Penyesuaian Alur']],

            // ── Kelulusan Modul (H05)
            ['code' => 'H05', 'variant' => 'result',       'name' => 'Module Graduation',   'description' => 'Lulus modul.',           'instructions' => [
                'next_action'   => AC::ACTION_FINISH_MATERIAL,
                'label'         => 'Selesaikan Modul',
                'message'       => 'Selamat! Kamu telah menyelesaikan modul ini.',
                'title'         => 'Kelulusan Modul!',
                'certification' => 'silver',
                'badges'        => ['module_complete', 'persistent_learner'],
            ]],

            // ── Intervensi Gaya Belajar (H06-H08, H11, H21)
            ['code' => 'H06', 'variant' => 'intervention', 'name' => 'Study Visual',        'description' => 'Arahkan ke materi visual.',   'instructions' => ['next_action' => AC::ACTION_STUDY_VISUAL,   'label' => 'Materi Visual',        'title' => 'Bantuan Adaptif']],
            ['code' => 'H07', 'variant' => 'intervention', 'name' => 'Study Textual',       'description' => 'Arahkan ke materi teks.',     'instructions' => ['next_action' => AC::ACTION_STUDY_TEXTUAL,  'label' => 'Materi Tekstual',      'title' => 'Bantuan Adaptif']],
            ['code' => 'H10', 'variant' => 'intervention', 'name' => 'Logic Guide',         'description' => 'Panduan logika/teori.',        'instructions' => ['next_action' => AC::ACTION_STUDY_THEORY,   'label' => 'Pahami Konsep',        'title' => 'Bantuan Adaptif']],
            ['code' => 'H11', 'variant' => 'intervention', 'name' => 'Syntax Guide',        'description' => 'Panduan sintaks.',             'instructions' => ['next_action' => AC::ACTION_STUDY_SYNTAX,   'label' => 'Pelajari Sintaks',     'title' => 'Bantuan Adaptif']],
            ['code' => 'H21', 'variant' => 'intervention', 'name' => 'Study Mixed',         'description' => 'Materi komprehensif.',         'instructions' => ['next_action' => AC::ACTION_STUDY_MIXED,    'label' => 'Materi Komprehensif',  'title' => 'Bantuan Adaptif']],

            // ── Psikologis & Motivasi (H17-H20)
            ['code' => 'H17', 'variant' => 'backtrack',    'name' => 'Anxiety Relief',      'description' => 'Turunkan beban cemas.',   'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY,  'label' => 'Mulai Santai',   'message' => 'Rileks, mari pelan-pelan. Kamu pasti bisa!', 'title' => 'Penyesuaian Alur']],
            ['code' => 'H18', 'variant' => 'acceleration', 'name' => 'Challenge Mode',      'description' => 'Beri tantangan tinggi.',  'instructions' => ['target_difficulty' => 'hard',    'next_action' => AC::ACTION_INCREASE_DIFFICULTY, 'label' => 'Mode Tantangan', 'message' => 'Terlalu mudah? Ayo naik level!', 'title' => 'Percepatan Aktif!']],
            ['code' => 'H19', 'variant' => 'result',       'name' => 'Motivational Msg',    'description' => 'Pesan semangat.',         'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Soal Berikutnya', 'message' => 'Pantang menyerah! Sedikit lagi benar.', 'title' => 'Tetap Semangat!']],
            ['code' => 'H20', 'variant' => 'result',       'name' => 'Careful Alert',       'description' => 'Peringatan ceroboh.',     'instructions' => ['next_action' => AC::ACTION_NEXT_QUESTION, 'label' => 'Soal Berikutnya', 'message' => 'Jangan terburu-buru, baca lagi dengan teliti.', 'title' => 'Hati-hati!']],

            // ── Krisis (H22-H23)
            ['code' => 'H22', 'variant' => 'intervention', 'name' => 'Crisis Intervention', 'description' => 'Intervensi skor nol.',    'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_STUDY_MATERIAL, 'label' => 'Pelajari Materi Dulu', 'message' => 'Sepertinya perlu mempelajari materinya lebih dulu. Mari mulai dari dasar!', 'title' => 'Bantuan Adaptif']],
            ['code' => 'H23', 'variant' => 'backtrack',    'name' => 'Persistent Fail Aid', 'description' => 'Bantuan gagal berturut.', 'instructions' => ['target_difficulty' => 'beginner', 'next_action' => AC::ACTION_REDUCE_DIFFICULTY, 'label' => 'Bimbingan Khusus', 'message' => 'Mari coba pendekatan berbeda dengan soal yang lebih mudah.', 'title' => 'Bantuan Adaptif']],
        ];

        foreach ($actions as $action) {
            $created                          = AdaptiveAction::create($action);
            $this->actionIds[$action['code']] = $created->id;
        }
    }

    private function seedRules(): void
    {
        // ──────────────────────────────────────────────────────────────────
        // TIER 1: DEDUKSI (RD01 - RD10)
        // Fokus: Menghasilkan Virtual Facts (V-codes). Action selalu H00.
        // ──────────────────────────────────────────────────────────────────
        $rules = [
            ['code' => 'RD01', 'name' => 'Detect High Performer',           'priority' =>  1, 'domain' => 'Deduction', 'required' => ['G03', 'G16', 'G37'], 'deduced' => ['V01'], 'action' => 'H00'],
            ['code' => 'RD02', 'name' => 'Detect Needs Foundation',         'priority' =>  2, 'domain' => 'Deduction', 'required' => ['G04'],               'deduced' => ['V02'], 'action' => 'H00'],
            ['code' => 'RD03', 'name' => 'Detect Crisis State',             'priority' =>  3, 'domain' => 'Deduction', 'required' => ['G28', 'G23'],         'deduced' => ['V03'], 'action' => 'H00'],
            ['code' => 'RD04', 'name' => 'Detect Style Mismatch Visual',    'priority' =>  4, 'domain' => 'Deduction', 'required' => ['G01', 'G09', 'G13'],  'deduced' => ['V04'], 'action' => 'H00'],
            ['code' => 'RD05', 'name' => 'Detect Style Mismatch Textual',   'priority' =>  5, 'domain' => 'Deduction', 'required' => ['G01', 'G10', 'G12'],  'deduced' => ['V05'], 'action' => 'H00'],
            ['code' => 'RD06', 'name' => 'Detect Mastery Ready (Beginner)', 'priority' =>  6, 'domain' => 'Deduction', 'required' => ['G06', 'G02'],          'deduced' => ['V06'], 'action' => 'H00'],
            ['code' => 'RD07', 'name' => 'Detect Mastery Ready (Medium)',   'priority' =>  7, 'domain' => 'Deduction', 'required' => ['G07', 'G02'],          'deduced' => ['V07'], 'action' => 'H00'],
            ['code' => 'RD08', 'name' => 'Detect Conceptual Gap',           'priority' =>  8, 'domain' => 'Deduction', 'required' => ['G01', 'G14'],          'deduced' => ['V08'], 'action' => 'H00'],
            ['code' => 'RD09', 'name' => 'Detect Careless Pattern',         'priority' =>  9, 'domain' => 'Deduction', 'required' => ['G01', 'G17'],          'deduced' => ['V09'], 'action' => 'H00'],
            ['code' => 'RD10', 'name' => 'Detect Struggle Pattern',         'priority' => 10, 'domain' => 'Deduction', 'required' => ['G01', 'G19'],          'deduced' => ['V10'], 'action' => 'H00'],

            // ──────────────────────────────────────────────────────────────────
            // TIER 2: AKSI DARI VIRTUAL FACTS (R11 - R21)
            // Fokus: Menanggapi hasil deduksi Tier 1.
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R11', 'name' => 'Boredom Challenge',       'priority' => 11, 'domain' => 'Progression', 'required' => ['V01', 'G21'],   'deduced' => null, 'action' => 'H18'],
            ['code' => 'R12', 'name' => 'Elite Jump',              'priority' => 12, 'domain' => 'Progression', 'required' => ['V01'],          'deduced' => null, 'action' => 'H03'],
            ['code' => 'R13', 'name' => 'Anxiety Safety Net',      'priority' => 13, 'domain' => 'Safety',      'required' => ['V03', 'G22'],   'deduced' => null, 'action' => 'H17'],
            ['code' => 'R14', 'name' => 'Promote to Medium',       'priority' => 14, 'domain' => 'Progression', 'required' => ['V06', 'G26'],    'deduced' => null, 'action' => 'H03'],
            ['code' => 'R15', 'name' => 'Promote to Hard',         'priority' => 15, 'domain' => 'Progression', 'required' => ['V07', 'G31'],    'deduced' => null, 'action' => 'H03'],
            ['code' => 'R16', 'name' => 'Foundation Recovery',     'priority' => 16, 'domain' => 'Safety',      'required' => ['V02'],           'deduced' => null, 'action' => 'H22'],
            ['code' => 'R17', 'name' => 'Concept Theory Guide',    'priority' => 17, 'domain' => 'Recovery',    'required' => ['V08'],           'deduced' => null, 'action' => 'H10'],
            ['code' => 'R18', 'name' => 'Careless Behavior Alert', 'priority' => 18, 'domain' => 'Recovery',    'required' => ['V09'],           'deduced' => null, 'action' => 'H20'],
            ['code' => 'R19', 'name' => 'Struggle Motivation',     'priority' => 19, 'domain' => 'Recovery',    'required' => ['V10'],           'deduced' => null, 'action' => 'H19'],
            ['code' => 'R20', 'name' => 'Direct to Visual (V)',    'priority' => 20, 'domain' => 'Style',       'required' => ['V04'],           'deduced' => null, 'action' => 'H06'],
            ['code' => 'R21', 'name' => 'Direct to Textual (V)',   'priority' => 21, 'domain' => 'Style',       'required' => ['V05'],           'deduced' => null, 'action' => 'H07'],

            // ──────────────────────────────────────────────────────────────────
            // TIER 3: INTERVENSI LANGSUNG (R22 - R26)
            // Fokus: Recovery dan Style berdasarkan observasi G-facts.
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R22', 'name' => 'Syntax Error Help',       'priority' => 22, 'domain' => 'Recovery',   'required' => ['G12', 'G01'],   'deduced' => null, 'action' => 'H11'],
            ['code' => 'R23', 'name' => 'Logic Error Help',        'priority' => 23, 'domain' => 'Recovery',   'required' => ['G13', 'G01'],   'deduced' => null, 'action' => 'H10'],
            ['code' => 'R24', 'name' => 'Visual Preference',       'priority' => 24, 'domain' => 'Style',      'required' => ['G01', 'G09'],   'deduced' => null, 'action' => 'H06'],
            ['code' => 'R25', 'name' => 'Textual Preference',      'priority' => 25, 'domain' => 'Style',      'required' => ['G01', 'G10'],   'deduced' => null, 'action' => 'H07'],
            ['code' => 'R26', 'name' => 'Mixed Preference',        'priority' => 26, 'domain' => 'Style',      'required' => ['G01', 'G11'],   'deduced' => null, 'action' => 'H21'],

            // ──────────────────────────────────────────────────────────────────
            // TIER 4: KELULUSAN & FALLBACK (R31 - R39)
            // Fokus: Alur standar dan penyelesaian.
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R36', 'name' => 'Streak Reward Promotion', 'priority' => 36, 'domain' => 'Progression', 'required' => ['G05', 'G02'],  'deduced' => null, 'action' => 'H03'],
            ['code' => 'R37', 'name' => 'Hint Dependent Guide',    'priority' => 37, 'domain' => 'Recovery',    'required' => ['G20', 'G02'],  'deduced' => null, 'action' => 'H10'],
            ['code' => 'R38', 'name' => 'Hard Mastery Graduation', 'priority' => 38, 'domain' => 'Progression', 'required' => ['G08', 'G15'],  'deduced' => null, 'action' => 'H05'],
            ['code' => 'R39', 'name' => 'Struggle Near End',       'priority' => 39, 'domain' => 'Recovery',    'required' => ['G29', 'G01'],  'deduced' => null, 'action' => 'H19'],
            ['code' => 'R40', 'name' => 'Hard Struggle Backtrack', 'priority' => 40, 'domain' => 'Recovery',    'required' => ['G32', 'G28'],  'deduced' => null, 'action' => 'H04'],
            ['code' => 'R41', 'name' => 'Fast Track Next Module',  'priority' => 30, 'domain' => 'Progression', 'required' => ['G38', 'G21'],  'deduced' => null, 'action' => 'H05'],
            ['code' => 'R42', 'name' => 'Review Previous Module',  'priority' => 25, 'domain' => 'Recovery',    'required' => ['G36', 'G23'],  'deduced' => null, 'action' => 'H22'],
            ['code' => 'R43', 'name' => 'In-Module Theory Review', 'priority' => 26, 'domain' => 'Recovery',    'required' => ['G33', 'G04'],  'deduced' => null, 'action' => 'H10'],
            ['code' => 'R31', 'name' => 'Graduation Check',        'priority' => 41, 'domain' => 'Progression', 'required' => ['G30', 'G34'],  'deduced' => null, 'action' => 'H05'],
            ['code' => 'R32', 'name' => 'Careless Failure',        'priority' => 42, 'domain' => 'Recovery',    'required' => ['G01', 'G17'],  'deduced' => null, 'action' => 'H20'],
            ['code' => 'R33', 'name' => 'Slow But Steady',         'priority' => 43, 'domain' => 'Progression', 'required' => ['G02', 'G18'],  'deduced' => null, 'action' => 'H19'],
            ['code' => 'R34', 'name' => 'Default Pass',            'priority' => 44, 'domain' => 'Progression', 'required' => ['G02'],         'deduced' => null, 'action' => 'H01'],
            ['code' => 'R35', 'name' => 'Default Remedial',        'priority' => 45, 'domain' => 'Progression', 'required' => ['G01'],         'deduced' => null, 'action' => 'H02'],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create([
                'rule_code'      => $rule['code'],
                'name'           => $rule['name'],
                'domain'         => $rule['domain'],
                'priority'       => $rule['priority'],
                'required_facts' => $this->resolveFactCodes($rule['required']),
                'deduced_facts'  => $rule['deduced'] ? $this->resolveFactCodes($rule['deduced']) : null,
                'action_id'      => $this->actionIds[$rule['action']],
                'is_active'      => true,
            ]);
        }
    }

    /**
     * Resolves mixed code arrays (G-codes + V-codes) to the database fact codes.
     * G-codes map to FactRegistry (stored as G01, G02, etc.).
     * V-codes are stored as-is since they are virtual facts not in adaptive_facts.
     */
    private function resolveFactCodes(array $codes): array
    {
        return array_map(function (string $code) {
            if (str_starts_with($code, 'V')) {
                return $code; // Virtual facts pass through as-is
            }

            return FactRegistry::getCodeByRaw($code) ?? $code;
        }, $codes);
    }
}
