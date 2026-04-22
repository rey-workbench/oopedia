<?php

namespace Database\Seeders;

use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdaptiveRuleSeeder extends Seeder
{
    /** @var array<string, int> H-code → adaptive_actions.id */
    private array $actionIds = [];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AdaptiveRule::truncate();
        AdaptiveAction::truncate();
        AdaptiveFact::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedFacts();
        $this->seedActions();
        $this->seedRules();
    }

    // ─── Kamus G-Codes ───────────────────────────────────────────────────────

    private function seedFacts(): void
    {
        $facts = [
            ['code' => 'G01', 'category' => 'Metrik Skor',  'name' => 'Score Critical',        'description' => 'Skor sangat rendah (< 40%) atau gagal total.'],
            ['code' => 'G02', 'category' => 'Metrik Skor',  'name' => 'Score Remedial',         'description' => 'Skor perlu perbaikan (40% - 69%).'],
            ['code' => 'G03', 'category' => 'Metrik Skor',  'name' => 'Score Standard',         'description' => 'Skor memenuhi standar kelulusan (70% - 89%).'],
            ['code' => 'G04', 'category' => 'Metrik Skor',  'name' => 'Score Mastery',          'description' => 'Skor mahir/sempurna (90% - 100%).'],
            ['code' => 'G05', 'category' => 'Metrik Waktu', 'name' => 'Time Fast',              'description' => 'Penyelesaian < 70% dari alokasi waktu default.'],
            ['code' => 'G06', 'category' => 'Metrik Waktu', 'name' => 'Time Slow',              'description' => 'Penyelesaian melebihi alokasi waktu default.'],
            ['code' => 'G07', 'category' => 'Gaya Belajar', 'name' => 'Style Visual',           'description' => 'Kecenderungan terhadap gaya belajar Visual.'],
            ['code' => 'G08', 'category' => 'Gaya Belajar', 'name' => 'Style Textual',          'description' => 'Kecenderungan terhadap gaya belajar Tekstual.'],
            ['code' => 'G09', 'category' => 'Tipe Error',   'name' => 'Error Syntax',           'description' => 'Kesalahan pada penulisan/sintaksis kode.'],
            ['code' => 'G10', 'category' => 'Tipe Error',   'name' => 'Error Logic',            'description' => 'Kesalahan pada alur logika program.'],
            ['code' => 'G11', 'category' => 'Tipe Error',   'name' => 'No Error',               'description' => 'Jawaban benar tanpa kesalahan.'],
            ['code' => 'G12', 'category' => 'Interaksi',    'name' => 'Hint Used',              'description' => 'Menggunakan bantuan (hint) saat menjawab.'],
            ['code' => 'G13', 'category' => 'Konteks',      'name' => 'In Module',              'description' => 'Siswa sedang berada di tengah modul.'],
            ['code' => 'G14', 'category' => 'Konteks',      'name' => 'Module Started',         'description' => 'Modul baru saja dimulai.'],
            ['code' => 'G15', 'category' => 'Kesulitan',    'name' => 'Diff Beginner',          'description' => 'Sedang mengerjakan tingkat kesulitan Easy.'],
            ['code' => 'G16', 'category' => 'Kesulitan',    'name' => 'Diff Medium',            'description' => 'Sedang mengerjakan tingkat kesulitan Medium.'],
            ['code' => 'G17', 'category' => 'Kesulitan',    'name' => 'Diff Hard',              'description' => 'Sedang mengerjakan tingkat kesulitan Advanced/Hard.'],
            ['code' => 'G18', 'category' => 'Kesulitan',    'name' => 'Final Project',          'description' => 'Sedang mengerjakan Proyek Akhir Modul.'],
            ['code' => 'G19', 'category' => 'Kesulitan',    'name' => 'Is Practice',            'description' => 'Soal latihan biasa (bukan proyek akhir).'],
            ['code' => 'G20', 'category' => 'Progres',      'name' => 'Next Unlocked',          'description' => 'Materi berikutnya sudah terbuka.'],
            ['code' => 'G21', 'category' => 'Progres',      'name' => 'Prev Unlocked',          'description' => 'Materi sebelumnya sudah terbuka.'],
            ['code' => 'G22', 'category' => 'Riwayat',      'name' => 'Persistent Fail',        'description' => 'Gagal menjawab benar ≥ 2 kali berturut-turut.'],
            ['code' => 'G23', 'category' => 'Progres',      'name' => 'Completed Module',       'description' => 'Modul saat ini telah diselesaikan.'],
            ['code' => 'G24', 'category' => 'Progres',      'name' => 'Completed All',          'description' => 'Seluruh modul dalam sistem telah selesai.'],
            ['code' => 'G25', 'category' => 'Interaksi',    'name' => 'High Engagement',        'description' => 'Tingkat keterlibatan mahasiswa tinggi.'],
            ['code' => 'G26', 'category' => 'Progres',      'name' => 'Satisfactory Progress',  'description' => 'Progres materi memadai (> 60%).'],
            ['code' => 'G27', 'category' => 'Gaya Belajar', 'name' => 'Style Mixed',            'description' => 'Gaya belajar campuran (Visual & Tekstual).'],
        ];

        foreach ($facts as $data) {
            AdaptiveFact::create($data);
        }
    }

    // ─── Kamus H-Codes ───────────────────────────────────────────────────────

    private function seedActions(): void
    {
        $actions = [
            ['code' => 'H01', 'name' => 'Visual Crisis Intervention',    'description' => 'Intervensi segera ke materi visual akibat kegagalan kritis.',          'instructions' => ['next_action' => 'STUDY_VISUAL',  'message' => 'Nilai sangat rendah. Tinjau kembali materi visual.']],
            ['code' => 'H02', 'name' => 'Textual Crisis Intervention',   'description' => 'Intervensi segera ke materi tekstual akibat kegagalan kritis.',        'instructions' => ['next_action' => 'STUDY_TEXTUAL', 'message' => 'Nilai sangat rendah. Fokus pada dokumentasi teks.']],
            ['code' => 'H03', 'name' => 'Syntax Recovery',               'description' => 'Arahan pemulihan pemahaman sintaksis kode.',                            'instructions' => ['next_action' => 'STUDY_SYNTAX',  'message' => 'Ada kesalahan penulisan. Cek kembali sintaksmu.']],
            ['code' => 'H04', 'name' => 'Logic Recovery',                'description' => 'Arahan pemulihan pemahaman alur logika program.',                       'instructions' => ['next_action' => 'STUDY_LOGIC',   'message' => 'Logika programmu perlu diperbaiki.']],
            ['code' => 'H05', 'name' => 'Standard Promotion',            'description' => 'Lanjut ke soal berikutnya secara normal.',                             'instructions' => ['next_action' => 'NEXT_QUESTION', 'message' => 'Bagus! Lanjut ke soal berikutnya.']],
            ['code' => 'H06', 'name' => 'Accelerated Jump',              'description' => 'Melompati tingkat kesulitan langsung ke Hard.',                         'instructions' => ['navigation.target_difficulty' => 'hard', 'next_action' => 'NEXT_QUESTION', 'message' => 'Luar biasa! Langsung ke tantangan tersulit.']],
            ['code' => 'H07', 'name' => 'Critical Backtracking',         'description' => 'Mundur ke tingkat kesulitan lebih rendah.',                             'instructions' => ['navigation.target_difficulty' => 'beginner', 'message' => 'Kesulitan terdeteksi. Mari perkuat dasar.']],
            ['code' => 'H08', 'name' => 'Module Graduation',             'description' => 'Kelulusan modul dan buka akses modul berikutnya.',                      'instructions' => ['unlock_next_module' => true, 'next_action' => 'FINISH_MATERIAL', 'message' => 'Selamat! Modul selesai.']],
            ['code' => 'H09', 'name' => 'Gold Certificate',              'description' => 'Sertifikat Emas untuk penguasaan sempurna tanpa bantuan.',              'instructions' => ['certification' => 'gold',   'message' => 'LUAR BIASA! Sertifikat EMAS diraih.']],
            ['code' => 'H10', 'name' => 'Silver Certificate',            'description' => 'Sertifikat Perak untuk kelulusan baik tanpa bantuan hint.',             'instructions' => ['certification' => 'silver', 'message' => 'HEBAT! Sertifikat PERAK diraih.']],
            ['code' => 'H11', 'name' => 'Bronze Certificate',            'description' => 'Sertifikat Perunggu untuk kelulusan standar dengan bantuan.',           'instructions' => ['certification' => 'bronze', 'message' => 'Bagus! Sertifikat PERUNGGU diraih.']],
            ['code' => 'H12', 'name' => 'Visual Project Revision',       'description' => 'Revisi proyek akhir berbasis materi visual.',                           'instructions' => ['next_action' => 'STUDY_VISUAL',  'message' => 'Proyek perlu revisi. Tinjau materi visual.']],
            ['code' => 'H13', 'name' => 'Textual Project Revision',      'description' => 'Revisi proyek akhir berbasis materi tekstual.',                         'instructions' => ['next_action' => 'STUDY_TEXTUAL', 'message' => 'Proyek perlu revisi. Pelajari dokumentasi teks.']],
            ['code' => 'H14', 'name' => 'Persistent Visual Safety Net',  'description' => 'Jaring pengaman materi visual untuk kegagalan berulang.',               'instructions' => ['next_action' => 'STUDY_VISUAL',  'message' => 'Penguatan Visual diperlukan.']],
            ['code' => 'H15', 'name' => 'Persistent Textual Safety Net', 'description' => 'Jaring pengaman materi tekstual untuk kegagalan berulang.',             'instructions' => ['next_action' => 'STUDY_TEXTUAL', 'message' => 'Penguatan Teks diperlukan.']],
            ['code' => 'H16', 'name' => 'Acceleration Material',         'description' => 'Lompatan langsung ke materi atau modul berikutnya.',                    'instructions' => ['next_action' => 'NEXT_MATERIAL', 'message' => 'Akselerasi materi berhasil!']],
        ];

        foreach ($actions as $data) {
            $action                         = AdaptiveAction::create($data);
            $this->actionIds[$data['code']] = $action->id;
        }
    }

    // ─── Rule Base (15 rules + 2 extras) ─────────────────────────────────────
    //
    // required_facts  = G-codes yang HARUS semua ada agar rule aktif
    // forbidden_facts = G-codes yang TIDAK BOLEH ada (opsional, default null)
    //
    //  Rule  | Kondisi (G)                           | Aksi (H)
    // -------+---------------------------------------+----------------------------
    //  R01   | G18, G02, G22, G07                    | H12 Visual Proj Revision
    //  R02   | G18, G02, G22, G08                    | H13 Textual Proj Revision
    //  R03   | G19, G02, G22, G07                    | H14 Visual Safety Net
    //  R04   | G19, G02, G22, G08                    | H15 Textual Safety Net
    //  R05   | G19, G01, G15, G07      (excl G22)    | H01 Visual Crisis
    //  R06   | G19, G01, G15, G08      (excl G22)    | H02 Textual Crisis
    //  R07   | G18, G02, G07           (excl G22)    | H12 Visual Proj Revision
    //  R08   | G18, G02, G08           (excl G22)    | H13 Textual Proj Revision
    //  R09   | G18, G03, G26, G05      (excl G12)    | H09 Gold Certificate
    //  R10   | G18, G03, G26           (excl G12,G05)| H10 Silver Certificate
    //  R11   | G18, G03, G26, G12                    | H11 Bronze Certificate
    //  R12   | G19, G02, G16, G12, G09 (excl G22)    | H03 Syntax Recovery
    //  R13   | G19, G02, G16, G12, G10 (excl G22)    | H04 Logic Recovery
    //  R14   | G19, G01, G17           (excl G22)    | H07 Critical Backtracking
    //  R15   | G19, G03, G17, G26, G05               | H08 Module Graduation
    //  RE1   | G19, G04                               | H06 Accelerated Jump
    //  RE2   | G19, G03                               | H05 Standard Promotion (fallback)

    private function seedRules(): void
    {
        $rules = [
            // ── Proyek Akhir: Revisi ───────────────────────────────────────────
            ['code' => 'R01', 'name' => 'Visual Project Revision (Stuck)',   'priority' =>  3, 'required' => ['G18', 'G02', 'G22', 'G07'], 'forbidden' => null,         'action' => 'H12'],
            ['code' => 'R02', 'name' => 'Textual Project Revision (Stuck)',  'priority' =>  4, 'required' => ['G18', 'G02', 'G22', 'G08'], 'forbidden' => null,         'action' => 'H13'],
            // ── Materi Standar: Safety Net ─────────────────────────────────────
            ['code' => 'R03', 'name' => 'Persistent Visual Safety Net',      'priority' =>  5, 'required' => ['G19', 'G02', 'G22', 'G07'], 'forbidden' => null,         'action' => 'H14'],
            ['code' => 'R04', 'name' => 'Persistent Textual Safety Net',     'priority' =>  6, 'required' => ['G19', 'G02', 'G22', 'G08'], 'forbidden' => null,         'action' => 'H15'],
            // ── Materi Standar: Intervensi Kritis (Dasar) ─────────────────────
            ['code' => 'R05', 'name' => 'Visual Crisis Intervention',        'priority' => 10, 'required' => ['G19', 'G01', 'G15', 'G07'], 'forbidden' => ['G22'],      'action' => 'H01'],
            ['code' => 'R06', 'name' => 'Textual Crisis Intervention',       'priority' => 11, 'required' => ['G19', 'G01', 'G15', 'G08'], 'forbidden' => ['G22'],      'action' => 'H02'],
            // ── Proyek Akhir: Revisi Pertama Kali ─────────────────────────────
            ['code' => 'R07', 'name' => 'Visual Project Revision (New)',     'priority' => 15, 'required' => ['G18', 'G02', 'G07'],       'forbidden' => ['G22'],      'action' => 'H12'],
            ['code' => 'R08', 'name' => 'Textual Project Revision (New)',    'priority' => 16, 'required' => ['G18', 'G02', 'G08'],       'forbidden' => ['G22'],      'action' => 'H13'],
            // ── Proyek Akhir: Sertifikasi ──────────────────────────────────────
            ['code' => 'R09', 'name' => 'Gold Certificate',                  'priority' => 21, 'required' => ['G18', 'G03', 'G26', 'G05'], 'forbidden' => ['G12'],      'action' => 'H09'],
            ['code' => 'R10', 'name' => 'Silver Certificate',                'priority' => 22, 'required' => ['G18', 'G03', 'G26'],       'forbidden' => ['G12', 'G05'], 'action' => 'H10'],
            ['code' => 'R11', 'name' => 'Bronze Certificate',                'priority' => 23, 'required' => ['G18', 'G03', 'G26', 'G12'], 'forbidden' => null,         'action' => 'H11'],
            // ── Materi Standar: Pemulihan Menengah ─────────────────────────────
            ['code' => 'R12', 'name' => 'Syntax Recovery',                   'priority' => 24, 'required' => ['G19', 'G02', 'G16', 'G12', 'G09'], 'forbidden' => ['G22'], 'action' => 'H03'],
            ['code' => 'R13', 'name' => 'Logic Recovery',                    'priority' => 25, 'required' => ['G19', 'G02', 'G16', 'G12', 'G10'], 'forbidden' => ['G22'], 'action' => 'H04'],
            // ── Materi Standar: Mundur Tingkat Sulit ──────────────────────────
            ['code' => 'R14', 'name' => 'Critical Backtracking',             'priority' => 27, 'required' => ['G19', 'G01', 'G17'],       'forbidden' => ['G22'],      'action' => 'H07'],
            // ── Materi Standar: Kelulusan Modul ───────────────────────────────
            ['code' => 'R15', 'name' => 'Module Graduation',                 'priority' => 30, 'required' => ['G19', 'G03', 'G17', 'G26', 'G05'], 'forbidden' => null,   'action' => 'H08'],
            // ── Extra: Lompatan & Fallback ─────────────────────────────────────
            ['code' => 'RE1', 'name' => 'Accelerated Jump',                  'priority' => 40, 'required' => ['G19', 'G04'],             'forbidden' => null,         'action' => 'H06'],
            ['code' => 'RE2', 'name' => 'Standard Promotion',                'priority' => 50, 'required' => ['G19', 'G03'],             'forbidden' => null,         'action' => 'H05'],
        ];

        foreach ($rules as $rule) {
            AdaptiveRule::create([
                'rule_code'      => $rule['code'],
                'name'           => $rule['name'],
                'priority'       => $rule['priority'],
                'required_facts' => $rule['required'],
                'forbidden_facts'=> $rule['forbidden'],
                'action_id'      => $this->actionIds[$rule['action']],
                'is_active'      => true,
            ]);
        }
    }
}
