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
            ['code' => AC::FACT_SCORE_FAILURE,  'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_SCORE_FAILURE],  'description' => 'Skor rendah (<70).'],
            ['code' => AC::FACT_SCORE_PASS,     'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_SCORE_PASS],     'description' => 'Skor cukup (70-89).'],
            ['code' => AC::FACT_SCORE_PERFECT,  'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_SCORE_PERFECT],  'description' => 'Skor sempurna (90+).'],
            ['code' => AC::FACT_SCORE_ZERO,     'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_SCORE_ZERO],     'description' => 'Salah total (0).'],

            // ── Konsistensi & Penguasaan (G05-G08)
            ['code' => AC::FACT_CONSISTENCY_HIGH,  'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_CONSISTENCY_HIGH],  'description' => 'Konsisten benar (streak ≥3).'],
            ['code' => AC::FACT_MASTERY_BEGINNER,  'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_MASTERY_BEGINNER],  'description' => 'Kuasai level beginner.'],
            ['code' => AC::FACT_MASTERY_MEDIUM,    'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_MASTERY_MEDIUM],    'description' => 'Kuasai level medium.'],
            ['code' => AC::FACT_MASTERY_HARD,      'category' => 'performance', 'name' => AC::FACT_NAMES[AC::FACT_MASTERY_HARD],      'description' => 'Kuasai level hard.'],

            // ── Gaya Belajar (G09-G11)
            ['code' => AC::FACT_STYLE_VISUAL,   'category' => 'style', 'name' => AC::FACT_NAMES[AC::FACT_STYLE_VISUAL],   'description' => 'Cenderung visual.'],
            ['code' => AC::FACT_STYLE_TEXTUAL,  'category' => 'style', 'name' => AC::FACT_NAMES[AC::FACT_STYLE_TEXTUAL],  'description' => 'Cenderung tekstual.'],
            ['code' => AC::FACT_STYLE_MIXED,    'category' => 'style', 'name' => AC::FACT_NAMES[AC::FACT_STYLE_MIXED],    'description' => 'Gaya belajar campuran.'],

            // ── Tipe Error (G12-G15)
            ['code' => AC::FACT_ERROR_SYNTAX,   'category' => 'error', 'name' => AC::FACT_NAMES[AC::FACT_ERROR_SYNTAX],   'description' => 'Sering salah tulis (syntax).'],
            ['code' => AC::FACT_ERROR_LOGIC,    'category' => 'error', 'name' => AC::FACT_NAMES[AC::FACT_ERROR_LOGIC],    'description' => 'Sering salah logika.'],
            ['code' => AC::FACT_ERROR_CONCEPT,  'category' => 'error', 'name' => AC::FACT_NAMES[AC::FACT_ERROR_CONCEPT],  'description' => 'Sering salah konsep.'],
            ['code' => AC::FACT_NO_ERROR,       'category' => 'error', 'name' => AC::FACT_NAMES[AC::FACT_NO_ERROR],       'description' => 'Tanpa kesalahan.'],

            // ── Waktu (G16-G19)
            ['code' => AC::FACT_TIME_FAST_SUCCESS,  'category' => 'time', 'name' => AC::FACT_NAMES[AC::FACT_TIME_FAST_SUCCESS],  'description' => 'Cepat & Benar.'],
            ['code' => AC::FACT_TIME_FAST_FAIL,     'category' => 'time', 'name' => AC::FACT_NAMES[AC::FACT_TIME_FAST_FAIL],     'description' => 'Cepat & Salah (Ceroboh).'],
            ['code' => AC::FACT_TIME_SLOW_SUCCESS,  'category' => 'time', 'name' => AC::FACT_NAMES[AC::FACT_TIME_SLOW_SUCCESS],  'description' => 'Lambat & Benar.'],
            ['code' => AC::FACT_TIME_SLOW_FAIL,     'category' => 'time', 'name' => AC::FACT_NAMES[AC::FACT_TIME_SLOW_FAIL],     'description' => 'Lambat & Salah (Struggle).'],

            // ── Kemandirian & Hint (G20, G37)
            ['code' => AC::FACT_HINT_USED,        'category' => 'behaviour', 'name' => AC::FACT_NAMES[AC::FACT_HINT_USED],        'description' => 'Menggunakan hint.'],
            ['code' => AC::FACT_INDEPENDENT_WORK, 'category' => 'behaviour', 'name' => AC::FACT_NAMES[AC::FACT_INDEPENDENT_WORK], 'description' => 'Bekerja tanpa hint.'],

            // ── Perilaku Psikologis (G21-G23)
            ['code' => AC::FACT_BOREDOM_SIGNS,  'category' => 'behaviour', 'name' => AC::FACT_NAMES[AC::FACT_BOREDOM_SIGNS],  'description' => 'Tanda kebosanan.'],
            ['code' => AC::FACT_ANXIETY_SIGNS,   'category' => 'behaviour', 'name' => AC::FACT_NAMES[AC::FACT_ANXIETY_SIGNS],   'description' => 'Tanda kecemasan.'],
            ['code' => AC::FACT_HIGH_STRUGGLE,   'category' => 'behaviour', 'name' => AC::FACT_NAMES[AC::FACT_HIGH_STRUGGLE],   'description' => 'Kesulitan tinggi.'],

            // ── Tingkat Kesulitan Saat Ini (G26, G31, G32)
            ['code' => AC::FACT_DIFF_BEGINNER,  'category' => 'difficulty', 'name' => AC::FACT_NAMES[AC::FACT_DIFF_BEGINNER],  'description' => 'Sedang di level beginner.'],
            ['code' => AC::FACT_DIFF_MEDIUM,    'category' => 'difficulty', 'name' => AC::FACT_NAMES[AC::FACT_DIFF_MEDIUM],    'description' => 'Sedang di level medium.'],
            ['code' => AC::FACT_DIFF_HARD,      'category' => 'difficulty', 'name' => AC::FACT_NAMES[AC::FACT_DIFF_HARD],      'description' => 'Sedang di level hard.'],

            // ── Progres & Modul (G28-G36)
            ['code' => AC::FACT_PERSISTENT_FAIL,       'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_PERSISTENT_FAIL],       'description' => 'Gagal berturut-turut.'],
            ['code' => AC::FACT_MODULE_NEARLY_DONE,    'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_MODULE_NEARLY_DONE],    'description' => 'Materi hampir selesai.'],
            ['code' => AC::FACT_MODULE_GRADUATION,     'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_MODULE_GRADUATION],     'description' => 'Layak lulus modul.'],
            ['code' => AC::FACT_IN_MODULE,             'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_IN_MODULE],             'description' => 'Sedang dalam modul pembelajaran.'],
            ['code' => AC::FACT_SATISFACTORY_PROGRESS, 'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_SATISFACTORY_PROGRESS], 'description' => 'Progres memadai.'],
            ['code' => AC::FACT_NEXT_UNLOCKED,         'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_NEXT_UNLOCKED],         'description' => 'Modul berikutnya sudah terbuka.'],
            ['code' => AC::FACT_NEXT_LOCKED,           'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_NEXT_LOCKED],           'description' => 'Modul berikutnya masih terkunci.'],
            ['code' => AC::FACT_PREV_UNLOCKED,         'category' => 'progress', 'name' => AC::FACT_NAMES[AC::FACT_PREV_UNLOCKED],         'description' => 'Modul sebelumnya sudah terbuka.'],

            // ── Virtual / Deduced Facts (V01-V07)
            ['code' => AC::V01_HIGH_PERFORMER,        'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V01_HIGH_PERFORMER],        'description' => 'Deduksi: siswa berkinerja tinggi dan mandiri.'],
            ['code' => AC::V02_NEEDS_FOUNDATION,      'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V02_NEEDS_FOUNDATION],      'description' => 'Deduksi: siswa perlu penguatan dasar.'],
            ['code' => AC::V03_IN_CRISIS,             'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V03_IN_CRISIS],             'description' => 'Deduksi: siswa dalam kondisi krisis belajar.'],
            ['code' => AC::V04_STYLE_MISMATCH_VISUAL, 'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V04_STYLE_MISMATCH_VISUAL], 'description' => 'Deduksi: siswa visual gagal karena soal berbasis logika/teks.'],
            ['code' => AC::V05_STYLE_MISMATCH_TEXTUAL, 'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V05_STYLE_MISMATCH_TEXTUAL], 'description' => 'Deduksi: siswa tekstual gagal karena soal berbasis sintaks.'],
            ['code' => AC::V06_MASTERY_READY_BEGINNER, 'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V06_MASTERY_READY_BEGINNER], 'description' => 'Deduksi: siswa siap naik dari Beginner ke Medium.'],
            ['code' => AC::V07_MASTERY_READY_MEDIUM,   'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V07_MASTERY_READY_MEDIUM],   'description' => 'Deduksi: siswa siap naik dari Medium ke Hard.'],
            ['code' => AC::V08_CONCEPTUAL_GAP,         'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V08_CONCEPTUAL_GAP],         'description' => 'Deduksi: siswa memiliki celah pemahaman konsep fundamental.'],
            ['code' => AC::V09_CARELESS_PATTERN,       'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V09_CARELESS_PATTERN],       'description' => 'Deduksi: siswa cenderung terburu-buru meski mampu.'],
            ['code' => AC::V10_STRUGGLE_PATTERN,       'category' => 'deduced', 'name' => AC::VIRTUAL_NAMES[AC::V10_STRUGGLE_PATTERN],       'description' => 'Deduksi: siswa menunjukkan pola kesulitan yang konsisten.'],
        ];

        foreach ($facts as $fact) {
            $created                      = AdaptiveFact::create($fact);
            $this->factIds[$fact['code']] = $created->id;
        }
    }

    private function seedActions(): void
    {
        $actions = [
            // ── H00: Silent – Khusus untuk rule Deduksi
            ['code' => AC::ACTION_DEDUCTION, 'variant' => 'silent', 'name' => AC::ACTION_NAMES[AC::ACTION_DEDUCTION], 'description' => 'Tidak ada aksi UI. Hanya menghasilkan virtual facts.', 'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_SILENT]],

            // ── Navigasi Soal (H01-H02)
            ['code' => AC::ACTION_STANDARD_PROMOTION, 'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_STANDARD_PROMOTION],  'description' => 'Lanjut normal.',         'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_NEXT_QUESTION,      AC::KEY_LABEL => 'Soal Berikutnya', AC::KEY_TITLE => 'Luar Biasa!']],
            ['code' => AC::ACTION_STANDARD_REMEDIAL,  'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_STANDARD_REMEDIAL],   'description' => 'Ulang soal.',            'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_NEXT_QUESTION,      AC::KEY_LABEL => 'Coba Lagi',       AC::KEY_MESSAGE => 'Jawaban kurang tepat. Ayo coba lagi!', AC::KEY_TITLE => 'Jangan Menyerah!']],

            // ── Percepatan & Penurunan Difficulty (H03-H04)
            ['code' => AC::ACTION_ACCELERATED_JUMP,   'variant' => 'acceleration', 'name' => AC::ACTION_NAMES[AC::ACTION_ACCELERATED_JUMP],    'description' => 'Lompat level.',          'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_HARD, AC::KEY_NEXT_ACTION => AC::ACTION_INCREASE_DIFFICULTY, AC::KEY_LABEL => 'Tantangan Baru', AC::KEY_MESSAGE => 'Luar Biasa! Tantangan level lebih tinggi menantimu!', AC::KEY_TITLE => 'Percepatan Aktif!']],
            ['code' => AC::ACTION_CRITICAL_BACKTRACK, 'variant' => 'backtrack',    'name' => AC::ACTION_NAMES[AC::ACTION_CRITICAL_BACKTRACK],  'description' => 'Turun level.',           'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_BEGINNER, AC::KEY_NEXT_ACTION => AC::ACTION_REDUCE_DIFFICULTY, AC::KEY_LABEL => 'Bimbingan Level', AC::KEY_MESSAGE => 'Kami menyesuaikan tingkat untuk kenyamanan belajarmu.', AC::KEY_TITLE => 'Penyesuaian Alur']],

            // ── Kelulusan Modul (H05)
            ['code' => AC::ACTION_MODULE_GRADUATION, 'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_MODULE_GRADUATION],   'description' => 'Lulus modul.',           'instructions' => [
                AC::KEY_NEXT_ACTION   => AC::ACTION_FINISH_MATERIAL,
                AC::KEY_LABEL         => 'Selesaikan Modul',
                AC::KEY_MESSAGE       => 'Selamat! Kamu telah menyelesaikan modul ini.',
                AC::KEY_TITLE         => 'Kelulusan Modul!',
                AC::KEY_CERTIFICATION => 'silver',
                AC::KEY_BADGES        => ['module_complete', 'persistent_learner'],
            ]],

            // ── Intervensi Gaya Belajar (H06-H08, H10-H11, H21)
            ['code' => AC::ACTION_STUDY_VISUAL_MAT,    'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_STUDY_VISUAL_MAT],    'description' => 'Arahkan ke materi visual.',   'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_VISUAL,   AC::KEY_LABEL => 'Materi Visual',        AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_STUDY_TEXTUAL_MAT,   'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_STUDY_TEXTUAL_MAT],   'description' => 'Arahkan ke materi teks.',     'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_TEXTUAL,  AC::KEY_LABEL => 'Materi Tekstual',      AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_LOGIC_GUIDE,         'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_LOGIC_GUIDE],         'description' => 'Panduan logika/teori.',        'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_THEORY,   AC::KEY_LABEL => 'Pahami Konsep',        AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_SYNTAX_GUIDE,        'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_SYNTAX_GUIDE],        'description' => 'Panduan sintaks.',             'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_SYNTAX,   AC::KEY_LABEL => 'Pelajari Sintaks',     AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_STUDY_MIXED_MAT,     'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_STUDY_MIXED_MAT],     'description' => 'Materi komprehensif.',         'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_MIXED,    AC::KEY_LABEL => 'Materi Komprehensif',  AC::KEY_TITLE => 'Bantuan Adaptif']],

            // ── Psikologis & Motivasi (H17-H20)
            ['code' => AC::ACTION_ANXIETY_RELIEF,      'variant' => 'backtrack',    'name' => AC::ACTION_NAMES[AC::ACTION_ANXIETY_RELIEF],      'description' => 'Turunkan beban cemas.',   'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_BEGINNER, AC::KEY_NEXT_ACTION => AC::ACTION_REDUCE_DIFFICULTY,  AC::KEY_LABEL => 'Mulai Santai',   AC::KEY_MESSAGE => 'Rileks, mari pelan-pelan. Kamu pasti bisa!', AC::KEY_TITLE => 'Penyesuaian Alur']],
            ['code' => AC::ACTION_CHALLENGE_MODE,      'variant' => 'acceleration', 'name' => AC::ACTION_NAMES[AC::ACTION_CHALLENGE_MODE],      'description' => 'Beri tantangan tinggi.',  'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_HARD,    AC::KEY_NEXT_ACTION => AC::ACTION_INCREASE_DIFFICULTY, AC::KEY_LABEL => 'Mode Tantangan', AC::KEY_MESSAGE => 'Terlalu mudah? Ayo naik level!', AC::KEY_TITLE => 'Percepatan Aktif!']],
            ['code' => AC::ACTION_MOTIVATIONAL_MSG,    'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_MOTIVATIONAL_MSG],    'description' => 'Pesan semangat.',         'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_NEXT_QUESTION, AC::KEY_LABEL => 'Soal Berikutnya', AC::KEY_MESSAGE => 'Pantang menyerah! Sedikit lagi benar.', AC::KEY_TITLE => 'Tetap Semangat!']],
            ['code' => AC::ACTION_CAREFUL_ALERT,       'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_CAREFUL_ALERT],       'description' => 'Peringatan ceroboh.',     'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_NEXT_QUESTION, AC::KEY_LABEL => 'Soal Berikutnya', AC::KEY_MESSAGE => 'Jangan terburu-buru, baca lagi dengan teliti.', AC::KEY_TITLE => 'Hati-hati!']],

            // ── Krisis (H22-H23)
            ['code' => AC::ACTION_CRISIS_INTERVENTION, 'variant' => 'intervention', 'name' => AC::ACTION_NAMES[AC::ACTION_CRISIS_INTERVENTION], 'description' => 'Intervensi skor nol.',    'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_BEGINNER, AC::KEY_NEXT_ACTION => AC::ACTION_STUDY_MATERIAL, AC::KEY_LABEL => 'Pelajari Materi Dulu', AC::KEY_MESSAGE => 'Sepertinya perlu mempelajari materinya lebih dulu. Mari mulai dari dasar!', AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_PREMIUM_PRAISE,      'variant' => 'result',       'name' => AC::ACTION_NAMES[AC::ACTION_PREMIUM_PRAISE],      'description' => 'Apresiasi kinerja tinggi.', 'instructions' => [AC::KEY_NEXT_ACTION => AC::ACTION_NEXT_QUESTION, AC::KEY_LABEL => 'Lanjutkan!', AC::KEY_MESSAGE => 'Performa luar biasa! Kamu menunjukkan penguasaan materi yang sangat mendalam.', AC::KEY_TITLE => 'Kinerja Elite!']],
            ['code' => AC::ACTION_PERSISTENT_FAIL_AID, 'variant' => 'backtrack',    'name' => AC::ACTION_NAMES[AC::ACTION_PERSISTENT_FAIL_AID], 'description' => 'Bantuan gagal berturut.', 'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_BEGINNER, AC::KEY_NEXT_ACTION => AC::ACTION_REDUCE_DIFFICULTY, AC::KEY_LABEL => 'Bimbingan Khusus', AC::KEY_MESSAGE => 'Mari coba pendekatan berbeda dengan soal yang lebih mudah.', AC::KEY_TITLE => 'Bantuan Adaptif']],
            ['code' => AC::ACTION_BOOST_TO_MEDIUM,     'variant' => 'acceleration', 'name' => AC::ACTION_NAMES[AC::ACTION_BOOST_TO_MEDIUM],     'description' => 'Naik ke level medium.',  'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_MEDIUM, AC::KEY_NEXT_ACTION => AC::ACTION_INCREASE_DIFFICULTY, AC::KEY_LABEL => 'Naik Level', AC::KEY_MESSAGE => 'Bagus! Kamu siap untuk tantangan menengah.', AC::KEY_TITLE => 'Progres Cepat']],
            ['code' => AC::ACTION_BOOST_TO_HARD,       'variant' => 'acceleration', 'name' => AC::ACTION_NAMES[AC::ACTION_BOOST_TO_HARD],       'description' => 'Naik ke level hard.',    'instructions' => [AC::KEY_TARGET_DIFFICULTY => AC::DIFFICULTY_HARD, AC::KEY_NEXT_ACTION => AC::ACTION_INCREASE_DIFFICULTY, AC::KEY_LABEL => 'Tantangan Ahli', AC::KEY_MESSAGE => 'Luar biasa! Mari uji kemampuanmu di level tersulit.', AC::KEY_TITLE => 'Percepatan Aktif!']],
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
        // ──────────────────────────────────────────────────────────────────
        $rules = [
            ['code' => 'RD01', 'name' => 'Detect High Performer',           'priority' =>  1, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_PERFECT, AC::FACT_TIME_FAST_SUCCESS, AC::FACT_INDEPENDENT_WORK, AC::FACT_CONSISTENCY_HIGH], 'deduced' => [AC::V01_HIGH_PERFORMER], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD02', 'name' => 'Detect Needs Foundation',         'priority' =>  2, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_ZERO],               'deduced' => [AC::V02_NEEDS_FOUNDATION], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD03', 'name' => 'Detect Crisis State',             'priority' =>  3, 'domain' => 'Deduction', 'required' => [AC::FACT_PERSISTENT_FAIL, AC::FACT_HIGH_STRUGGLE],         'deduced' => [AC::V03_IN_CRISIS], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD04', 'name' => 'Detect Style Mismatch Visual',    'priority' =>  4, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_STYLE_VISUAL, AC::FACT_ERROR_LOGIC],  'deduced' => [AC::V04_STYLE_MISMATCH_VISUAL], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD05', 'name' => 'Detect Style Mismatch Textual',   'priority' =>  5, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_STYLE_TEXTUAL, AC::FACT_ERROR_SYNTAX],  'deduced' => [AC::V05_STYLE_MISMATCH_TEXTUAL], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD06', 'name' => 'Detect Mastery Ready (Beginner)', 'priority' =>  6, 'domain' => 'Deduction', 'required' => [AC::FACT_MASTERY_BEGINNER, AC::FACT_SCORE_PASS],          'deduced' => [AC::V06_MASTERY_READY_BEGINNER], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD07', 'name' => 'Detect Mastery Ready (Medium)',   'priority' =>  7, 'domain' => 'Deduction', 'required' => [AC::FACT_MASTERY_MEDIUM, AC::FACT_SCORE_PASS],          'deduced' => [AC::V07_MASTERY_READY_MEDIUM], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD08', 'name' => 'Detect Conceptual Gap',           'priority' =>  8, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_ERROR_CONCEPT],          'deduced' => [AC::V08_CONCEPTUAL_GAP], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD09', 'name' => 'Detect Careless Pattern',         'priority' =>  9, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_TIME_FAST_FAIL],          'deduced' => [AC::V09_CARELESS_PATTERN], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD10', 'name' => 'Detect Struggle Pattern',         'priority' => 10, 'domain' => 'Deduction', 'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_TIME_SLOW_FAIL],          'deduced' => [AC::V10_STRUGGLE_PATTERN], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD11', 'name' => 'Detect Speed Demon',              'priority' => 11, 'domain' => 'Deduction', 'required' => [AC::FACT_TIME_FAST_SUCCESS, AC::FACT_CONSISTENCY_HIGH], 'deduced' => [AC::V11_SPEED_DEMON], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD12', 'name' => 'Detect Meticulous Solver',        'priority' => 12, 'domain' => 'Deduction', 'required' => [AC::FACT_TIME_SLOW_SUCCESS, AC::FACT_NO_ERROR],         'deduced' => [AC::V12_METICULOUS_SOLVER], 'action' => AC::ACTION_DEDUCTION],
            ['code' => 'RD13', 'name' => 'Detect Unstoppable Force',        'priority' => 13, 'domain' => 'Deduction', 'required' => [AC::FACT_MASTERY_HARD, AC::FACT_SCORE_PERFECT],          'deduced' => [AC::V13_UNSTOPPABLE_FORCE], 'action' => AC::ACTION_DEDUCTION],

            // ──────────────────────────────────────────────────────────────────
            // TIER 2: AKSI PERSONA & PERCEPATAN (R11 - R12, R44 - R47)
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R11', 'name' => 'Boredom Challenge',       'priority' => 11, 'domain' => 'Progression', 'required' => [AC::V01_HIGH_PERFORMER, AC::FACT_BOREDOM_SIGNS],   'deduced' => null, 'action' => AC::ACTION_CHALLENGE_MODE],
            ['code' => 'R12', 'name' => 'Elite Jump to Medium',    'priority' => 12, 'domain' => 'Progression', 'required' => [AC::V01_HIGH_PERFORMER, AC::FACT_DIFF_BEGINNER], 'deduced' => null, 'action' => AC::ACTION_BOOST_TO_MEDIUM],
            ['code' => 'R44', 'name' => 'Speed Praise',            'priority' =>  5, 'domain' => 'Progression', 'required' => [AC::V11_SPEED_DEMON],           'deduced' => null, 'action' => AC::ACTION_PREMIUM_PRAISE],
            ['code' => 'R45', 'name' => 'Diligence Praise',        'priority' =>  6, 'domain' => 'Progression', 'required' => [AC::V12_METICULOUS_SOLVER],     'deduced' => null, 'action' => AC::ACTION_PREMIUM_PRAISE],
            ['code' => 'R46', 'name' => 'Elite Mastery Praise',    'priority' =>  1, 'domain' => 'Progression', 'required' => [AC::V13_UNSTOPPABLE_FORCE],     'deduced' => null, 'action' => AC::ACTION_PREMIUM_PRAISE],
            ['code' => 'R47', 'name' => 'Elite Jump to Hard',      'priority' => 12, 'domain' => 'Progression', 'required' => [AC::V01_HIGH_PERFORMER, AC::FACT_DIFF_MEDIUM],   'deduced' => null, 'action' => AC::ACTION_BOOST_TO_HARD],

            // ──────────────────────────────────────────────────────────────────
            // TIER 3: INTERVENSI & GAYA BELAJAR (R13 - R26)
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R13', 'name' => 'Anxiety Safety Net',      'priority' => 13, 'domain' => 'Safety',      'required' => [AC::V03_IN_CRISIS, AC::FACT_ANXIETY_SIGNS],   'deduced' => null, 'action' => AC::ACTION_ANXIETY_RELIEF],
            ['code' => 'R16', 'name' => 'Foundation Recovery',     'priority' => 16, 'domain' => 'Safety',      'required' => [AC::V02_NEEDS_FOUNDATION],           'deduced' => null, 'action' => AC::ACTION_CRISIS_INTERVENTION],
            ['code' => 'R17', 'name' => 'Concept Theory Guide',    'priority' => 17, 'domain' => 'Recovery',    'required' => [AC::V08_CONCEPTUAL_GAP],           'deduced' => null, 'action' => AC::ACTION_LOGIC_GUIDE],
            ['code' => 'R18', 'name' => 'Careless Behavior Alert', 'priority' => 18, 'domain' => 'Recovery',    'required' => [AC::V09_CARELESS_PATTERN],           'deduced' => null, 'action' => AC::ACTION_CAREFUL_ALERT],
            ['code' => 'R19', 'name' => 'Struggle Motivation',     'priority' => 19, 'domain' => 'Recovery',    'required' => [AC::V10_STRUGGLE_PATTERN],           'deduced' => null, 'action' => AC::ACTION_MOTIVATIONAL_MSG],
            ['code' => 'R20', 'name' => 'Direct to Visual (V)',    'priority' => 20, 'domain' => 'Style',       'required' => [AC::V04_STYLE_MISMATCH_VISUAL],           'deduced' => null, 'action' => AC::ACTION_STUDY_VISUAL_MAT],
            ['code' => 'R21', 'name' => 'Direct to Textual (V)',   'priority' => 21, 'domain' => 'Style',       'required' => [AC::V05_STYLE_MISMATCH_TEXTUAL],          'deduced' => null, 'action' => AC::ACTION_STUDY_TEXTUAL_MAT],
            ['code' => 'R22', 'name' => 'Syntax Error Help',       'priority' => 22, 'domain' => 'Recovery',   'required' => [AC::FACT_ERROR_SYNTAX, AC::FACT_SCORE_FAILURE],   'deduced' => null, 'action' => AC::ACTION_SYNTAX_GUIDE],
            ['code' => 'R23', 'name' => 'Logic Error Help',        'priority' => 23, 'domain' => 'Recovery',   'required' => [AC::FACT_ERROR_LOGIC, AC::FACT_SCORE_FAILURE],   'deduced' => null, 'action' => AC::ACTION_LOGIC_GUIDE],
            ['code' => 'R24', 'name' => 'Visual Preference',       'priority' => 24, 'domain' => 'Style',      'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_STYLE_VISUAL],   'deduced' => null, 'action' => AC::ACTION_STUDY_VISUAL_MAT],
            ['code' => 'R25', 'name' => 'Textual Preference',      'priority' => 25, 'domain' => 'Style',      'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_STYLE_TEXTUAL],   'deduced' => null, 'action' => AC::ACTION_STUDY_TEXTUAL_MAT],
            ['code' => 'R26', 'name' => 'Mixed Preference',        'priority' => 26, 'domain' => 'Style',      'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_STYLE_MIXED],   'deduced' => null, 'action' => AC::ACTION_STUDY_MIXED_MAT],

            // ──────────────────────────────────────────────────────────────────
            // TIER 4: PROGRESI & PEMULIHAN (R14 - R43)
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R14', 'name' => 'Promote to Medium',       'priority' => 14, 'domain' => 'Progression', 'required' => [AC::V06_MASTERY_READY_BEGINNER, AC::FACT_DIFF_BEGINNER],    'deduced' => null, 'action' => AC::ACTION_BOOST_TO_MEDIUM],
            ['code' => 'R15', 'name' => 'Promote to Hard',         'priority' => 15, 'domain' => 'Progression', 'required' => [AC::V07_MASTERY_READY_MEDIUM, AC::FACT_DIFF_MEDIUM],    'deduced' => null, 'action' => AC::ACTION_BOOST_TO_HARD],
            ['code' => 'R32', 'name' => 'Careless Failure',        'priority' => 42, 'domain' => 'Recovery',    'required' => [AC::FACT_SCORE_FAILURE, AC::FACT_TIME_FAST_FAIL],  'deduced' => null, 'action' => AC::ACTION_CAREFUL_ALERT],
            ['code' => 'R33', 'name' => 'Slow But Steady',         'priority' => 43, 'domain' => 'Progression', 'required' => [AC::FACT_SCORE_PASS, AC::FACT_TIME_SLOW_SUCCESS],  'deduced' => null, 'action' => AC::ACTION_MOTIVATIONAL_MSG],
            ['code' => 'R36', 'name' => 'Streak Reward Promotion', 'priority' => 36, 'domain' => 'Progression', 'required' => [AC::FACT_CONSISTENCY_HIGH, AC::FACT_SCORE_PASS],  'deduced' => null, 'action' => AC::ACTION_BOOST_TO_MEDIUM],
            ['code' => 'R37', 'name' => 'Hint Dependent Guide',    'priority' => 37, 'domain' => 'Recovery',    'required' => [AC::FACT_HINT_USED, AC::FACT_SCORE_PASS],  'deduced' => null, 'action' => AC::ACTION_LOGIC_GUIDE],
            ['code' => 'R39', 'name' => 'Struggle Near End',       'priority' => 39, 'domain' => 'Recovery',    'required' => [AC::FACT_MODULE_NEARLY_DONE, AC::FACT_SCORE_FAILURE],  'deduced' => null, 'action' => AC::ACTION_MOTIVATIONAL_MSG],
            ['code' => 'R40', 'name' => 'Hard Struggle Backtrack', 'priority' => 40, 'domain' => 'Recovery',    'required' => [AC::FACT_DIFF_HARD, AC::FACT_PERSISTENT_FAIL],  'deduced' => null, 'action' => AC::ACTION_CRITICAL_BACKTRACK],
            ['code' => 'R42', 'name' => 'Review Previous Module',  'priority' => 25, 'domain' => 'Recovery',    'required' => [AC::FACT_PREV_UNLOCKED, AC::FACT_HIGH_STRUGGLE],  'deduced' => null, 'action' => AC::ACTION_CRISIS_INTERVENTION],
            ['code' => 'R43', 'name' => 'In-Module Theory Review', 'priority' => 26, 'domain' => 'Recovery',    'required' => [AC::FACT_IN_MODULE, AC::FACT_SCORE_ZERO],  'deduced' => null, 'action' => AC::ACTION_LOGIC_GUIDE],

            // ──────────────────────────────────────────────────────────────────
            // TIER 5: KELULUSAN (GRADUATION) (R31, R38, R41)
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R31', 'name' => 'Graduation Check',        'priority' => 41, 'domain' => 'Progression', 'required' => [AC::FACT_MODULE_GRADUATION, AC::FACT_SATISFACTORY_PROGRESS],  'deduced' => null, 'action' => AC::ACTION_MODULE_GRADUATION],
            ['code' => 'R38', 'name' => 'Hard Mastery Graduation', 'priority' => 38, 'domain' => 'Progression', 'required' => [AC::FACT_MASTERY_HARD, AC::FACT_NO_ERROR, AC::FACT_MODULE_GRADUATION, AC::FACT_IN_MODULE, AC::FACT_SATISFACTORY_PROGRESS],  'deduced' => null, 'action' => AC::ACTION_MODULE_GRADUATION],
            ['code' => 'R41', 'name' => 'Fast Track Next Module',  'priority' => 30, 'domain' => 'Progression', 'required' => [AC::FACT_NEXT_LOCKED, AC::FACT_BOREDOM_SIGNS, AC::FACT_MODULE_GRADUATION, AC::FACT_IN_MODULE, AC::FACT_SATISFACTORY_PROGRESS],  'deduced' => null, 'action' => AC::ACTION_MODULE_GRADUATION],

            // ──────────────────────────────────────────────────────────────────
            // TIER 6: FALLBACK (R34, R35)
            // ──────────────────────────────────────────────────────────────────
            ['code' => 'R34', 'name' => 'Default Pass',            'priority' => 44, 'domain' => 'Progression', 'required' => [AC::FACT_SCORE_PASS],         'deduced' => null, 'action' => AC::ACTION_STANDARD_PROMOTION],
            ['code' => 'R35', 'name' => 'Default Remedial',        'priority' => 45, 'domain' => 'Progression', 'required' => [AC::FACT_SCORE_FAILURE],         'deduced' => null, 'action' => AC::ACTION_STANDARD_REMEDIAL],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::updateOrCreate(
                ['rule_code' => $rule['code']],
                [
                    'name'           => $rule['name'],
                    'domain'         => $rule['domain'],
                    'priority'       => $rule['priority'],
                    'required_facts' => $this->resolveFactCodes($rule['required']),
                    'deduced_facts'  => $rule['deduced'] ? $this->resolveFactCodes($rule['deduced']) : null,
                    'action_id'      => $this->actionIds[$rule['action']],
                    'is_active'      => true,
                ]
            );
        }

        // Clear adaptive rules cache
        \Illuminate\Support\Facades\Cache::forget('adaptive_rules_all');
        \Illuminate\Support\Facades\Cache::forget('adaptive_rules_count');
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
