<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Adaptive\AdaptiveActionId;
use App\Models\AdaptiveAction;
use App\Models\AdaptiveFact;
use App\Models\AdaptiveRule;
use Illuminate\Database\Seeder;

class AdaptiveRuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFacts();
        $this->seedActions();
        $this->seedRules();
    }

    private function seedFacts(): void
    {
        $facts = [
            // --- KELOMPOK G-CODES (FAKTA TERAMATI) ---

            // 1. Akurasi Keseluruhan (G01-G04, G17-G18)
            ['id' => 'G01', 'name' => 'Akurasi < 40%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => '<', 'val' => 40])],
            ['id' => 'G02', 'name' => 'Akurasi 40-60%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => 'between', 'val' => [40, 60]])],
            ['id' => 'G03', 'name' => 'Akurasi 60-70%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => 'between', 'val' => [60, 70]])],
            ['id' => 'G18', 'name' => 'Akurasi 70-80%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => 'between', 'val' => [70, 80]])],
            ['id' => 'G04', 'name' => 'Akurasi 80-90%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => 'between', 'val' => [80, 90]])],
            ['id' => 'G17', 'name' => 'Akurasi > 90%', 'category' => 'primary', 'logic' => json_encode(['key' => 'accuracy', 'op' => '>', 'val' => 90])],

            // 2. Tren Performa (G05-G07)
            ['id' => 'G05', 'name' => 'Tren Menurun', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.trend', 'op' => '==', 'val' => 'down'])],
            ['id' => 'G06', 'name' => 'Tren Stabil', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.trend', 'op' => '==', 'val' => 'stable'])],
            ['id' => 'G07', 'name' => 'Tren Meningkat', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.trend', 'op' => '==', 'val' => 'up'])],

            // 3. Waktu Respon Terakhir (G11-G13)
            ['id' => 'G11', 'name' => 'Respon Sangat Cepat (<10s)', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_response_time', 'op' => '<', 'val' => 10])],
            ['id' => 'G13', 'name' => 'Respon Normal (10-45s)', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_response_time', 'op' => 'between', 'val' => [10, 45]])],
            ['id' => 'G12', 'name' => 'Respon Lambat (>45s)', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_response_time', 'op' => '>', 'val' => 45])],

            // 4. Penggunaan Bantuan (G08-G10, G20)
            ['id' => 'G08', 'name' => 'Ketergantungan Hint (>3x)', 'category' => 'primary', 'logic' => json_encode(['key' => 'hints_used', 'op' => '>', 'val' => 3])],
            ['id' => 'G09', 'name' => 'Hint Sedang (2-3x)', 'category' => 'primary', 'logic' => json_encode(['key' => 'hints_used', 'op' => 'between', 'val' => [2, 3]])],
            ['id' => 'G10', 'name' => 'Hint Minimal (1x)', 'category' => 'primary', 'logic' => json_encode(['key' => 'hints_used', 'op' => '==', 'val' => 1])],
            ['id' => 'G20', 'name' => 'Tanpa Bantuan (0x)', 'category' => 'primary', 'logic' => json_encode(['key' => 'hints_used', 'op' => '==', 'val' => 0])],

            // 5. Hasil Jawaban Terakhir (G21-G22)
            ['id' => 'G21', 'name' => 'Jawaban Terakhir Benar', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_result', 'op' => '==', 'val' => true])],
            ['id' => 'G22', 'name' => 'Jawaban Terakhir Salah', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_result', 'op' => '==', 'val' => false])],
            ['id' => 'G23', 'name' => 'Gunakan Hint Sekarang', 'category' => 'primary', 'logic' => json_encode(['key' => 'performance_metrics.last_used_hint', 'op' => '==', 'val' => true])],

            // 6. Gamifikasi & Progres (G14-G16, G19)
            ['id' => 'G14', 'name' => 'Streak Aktif (>=3 hari)', 'category' => 'primary', 'logic' => json_encode(['key' => 'streak', 'op' => '>=', 'val' => 3])],
            ['id' => 'G15', 'name' => 'Streak Kuat (>=5 hari)', 'category' => 'primary', 'logic' => json_encode(['key' => 'streak', 'op' => '>=', 'val' => 5])],
            ['id' => 'G16', 'name' => 'Streak Legendaris (>=10 hari)', 'category' => 'primary', 'logic' => json_encode(['key' => 'streak', 'op' => '>=', 'val' => 10])],
            ['id' => 'G19', 'name' => 'Level Expert', 'category' => 'primary', 'logic' => json_encode(['key' => 'level', 'op' => '==', 'val' => 'Expert'])],

            // --- KELOMPOK V-CODES (DIAGNOSA INFERRED) ---
            ['id' => 'V01', 'name' => 'Krisis Pembelajaran', 'category' => 'virtual', 'logic' => null],
            ['id' => 'V02', 'name' => 'Sedang Kesulitan', 'category' => 'virtual', 'logic' => null],
            ['id' => 'V03', 'name' => 'Performa Optimal', 'category' => 'virtual', 'logic' => null],
            ['id' => 'V04', 'name' => 'Ketergantungan Bantuan', 'category' => 'virtual', 'logic' => null],
            ['id' => 'V05', 'name' => 'Potensi Menebak', 'category' => 'virtual', 'logic' => null],
        ];

        foreach ($facts as $fact) {
            AdaptiveFact::updateOrCreate(['id' => $fact['id']], $fact);
        }
    }

    private function seedActions(): void
    {
        $id = AdaptiveActionId::class;

        $actions = [
            ['id' => $id::FEEDBACK->value, 'name' => 'Lanjutkan Latihan', 'description' => 'Navigasi normal ke soal berikutnya', 'variant' => 'feedback'],
            ['id' => $id::GIVE_HINT->value, 'name' => 'Bonus Bantuan', 'description' => 'Berikan +1 Hint tambahan', 'variant' => 'popup'],
            ['id' => $id::REMEDIAL->value, 'name' => 'Remedial Standard', 'description' => 'Ulangi materi dari awal', 'variant' => 'feedback'],
            ['id' => $id::REMEDIAL_INTENSIVE->value, 'name' => 'Remedial Intensif', 'description' => 'Remedial + soal mudah', 'variant' => 'feedback'],
            ['id' => $id::REDUCE_DIFF->value, 'name' => 'Turunkan Kesulitan', 'description' => 'Ganti soal ke level lebih rendah', 'variant' => 'feedback'],
            ['id' => $id::INCREASE_DIFF->value, 'name' => 'Naikkan Kesulitan', 'description' => 'Tantangan level lebih tinggi', 'variant' => 'feedback'],
            ['id' => $id::REDUCE_HINT->value, 'name' => 'Batasi Bantuan', 'description' => 'Fokus pada kemampuan mandirimu!', 'variant' => 'popup'],
            ['id' => $id::NEW_CHALLENGE->value, 'name' => 'Tantangan Kilat', 'description' => 'Jawab soal berikutnya secepat mungkin!', 'variant' => 'challenge'],
            ['id' => $id::STREAK_BONUS->value, 'name' => 'Bonus Streak', 'description' => 'XP tambahan untuk mahasiswa', 'variant' => 'popup'],
            ['id' => $id::CERTIFICATION->value, 'name' => 'Berikan Sertifikat', 'description' => 'Capaian tertinggi mahasiswa', 'variant' => 'feedback'],
            ['id' => $id::SHOW_GUIDANCE->value, 'name' => 'Tampilkan Bimbingan', 'description' => 'Pesan pendukung pedagogis', 'variant' => 'feedback'],
            ['id' => $id::NOTIFY_TEACHER->value, 'name' => 'Lapor Pengajar', 'description' => 'Peringatan krisis ke admin', 'variant' => 'background_notification'],
        ];

        foreach ($actions as $action) {
            AdaptiveAction::updateOrCreate(['id' => $action['id']], $action + ['instructions' => []]);
        }
    }

    private function seedRules(): void
    {
        $id = AdaptiveActionId::class;

        $rules = [
            // --- DIAGNOSTIC RULES (DEDUCING VIRTUAL FACTS) ---
            [
                'id'                => 'R01',
                'priority'          => 10,
                'name'              => 'Analisa Performa Optimal',
                'recommendation'    => 'Menganalisa data akurasi dan kecepatan untuk menentukan status penguasaan tinggi.',
                'required_fact_ids' => ['G21', 'G17', 'G11'],
                'deduced_fact_ids'  => ['V03'],
                'actions'           => [],
            ],
            [
                'id'                => 'R02',
                'priority'          => 9,
                'name'              => 'Analisa Krisis Belajar',
                'recommendation'    => 'Mendeteksi penurunan performa drastis yang memerlukan intervensi segera.',
                'required_fact_ids' => ['G22', 'G01', 'G05'],
                'deduced_fact_ids'  => ['V01'],
                'actions'           => [],
            ],
            [
                'id'                => 'R03',
                'priority'          => 8,
                'name'              => 'Analisa Kesulitan Materi',
                'recommendation'    => 'Menganalisa apakah mahasiswa memerlukan penyesuaian tingkat kesulitan.',
                'required_fact_ids' => ['G22', 'G02', 'G12'],
                'deduced_fact_ids'  => ['V02'],
                'actions'           => [],
            ],
            [
                'id'                => 'R04',
                'priority'          => 7,
                'name'              => 'Analisa Pola Bantuan',
                'recommendation'    => 'Mengevaluasi tingkat ketergantungan mahasiswa pada fitur bantuan/hint.',
                'required_fact_ids' => ['G08', 'G12'],
                'deduced_fact_ids'  => ['V04'],
                'actions'           => [],
            ],
            [
                'id'                => 'R05',
                'priority'          => 6,
                'name'              => 'Analisa Potensi Menebak',
                'recommendation'    => 'Mendeteksi jawaban cepat dengan akurasi rendah yang mengarah pada perilaku menebak.',
                'required_fact_ids' => ['G11', 'G01'],
                'deduced_fact_ids'  => ['V05'],
                'actions'           => [],
            ],

            // --- INTERVENTION RULES (USING DIAGNOSIS) ---
            [
                'id'                => 'R06',
                'priority'          => 5,
                'name'              => 'Strategi Akselerasi',
                'recommendation'    => 'Memberikan tantangan lebih sulit dan bonus motivasi untuk performa optimal.',
                'required_fact_ids' => ['V03', 'G20'],
                'actions'           => [$id::INCREASE_DIFF->value, $id::NEW_CHALLENGE->value, $id::STREAK_BONUS->value],
            ],
            [
                'id'                => 'R07',
                'priority'          => 4,
                'name'              => 'Strategi Intervensi Krisis',
                'recommendation'    => 'Mengaktifkan jalur remedial intensif dan notifikasi ke pengajar.',
                'required_fact_ids' => ['V01'],
                'actions'           => [$id::REMEDIAL_INTENSIVE->value, $id::NOTIFY_TEACHER->value],
            ],
            [
                'id'                => 'R08',
                'priority'          => 3,
                'name'              => 'Strategi Adaptasi Kesulitan',
                'recommendation'    => 'Menurunkan tingkat kesulitan soal dan memberikan bantuan tambahan.',
                'required_fact_ids' => ['V02'],
                'actions'           => [$id::REDUCE_DIFF->value, $id::GIVE_HINT->value, $id::SHOW_GUIDANCE->value],
            ],
            [
                'id'                => 'R09',
                'priority'          => 2,
                'name'              => 'Strategi Penguatan Mandiri',
                'recommendation'    => 'Membatasi penggunaan hint untuk mendorong kemandirian belajar.',
                'required_fact_ids' => ['V04'],
                'actions'           => [$id::REDUCE_HINT->value],
            ],
            [
                'id'                => 'R10',
                'priority'          => 1,
                'name'              => 'Strategi Bimbingan Fokus',
                'recommendation'    => 'Memberikan panduan agar mahasiswa lebih teliti membaca soal.',
                'required_fact_ids' => ['V05'],
                'actions'           => [$id::SHOW_GUIDANCE->value],
            ],
            [
                'id'                => 'R11',
                'priority'          => 0,
                'name'              => 'Strategi Kelulusan Materi',
                'recommendation'    => 'Memberikan sertifikasi penguasaan materi bagi mahasiswa yang mencapai level puncak.',
                'required_fact_ids' => ['G19', 'G17'],
                'actions'           => [$id::CERTIFICATION->value],
            ],
            [
                'id'                => 'R00',
                'priority'          => 100,
                'name'              => 'Progres Terjaga',
                'recommendation'    => 'Teruslah melangkah! Setiap soal yang kamu kerjakan membawamu lebih dekat ke penguasaan materi.',
                'required_fact_ids' => [],
                'actions'           => [$id::FEEDBACK->value],
            ],
        ];

        foreach ($rules as $ruleData) {
            AdaptiveRule::updateOrCreate(['id' => $ruleData['id']], $ruleData + ['is_active' => true]);
        }
    }
}
