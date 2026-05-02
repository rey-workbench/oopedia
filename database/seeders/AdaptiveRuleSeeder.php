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
            // ==========================================================
            // KATEGORI 1: ELITE PERFORMANCE (PRIORITAS 0)
            // ==========================================================
            [
                'id'                => 'R01',
                'priority'          => 0,
                'name'              => 'Akselerasi & Tantangan Kilat',
                'recommendation'    => 'Luar biasa! Kamu memahami materi ini dengan sangat cepat. Mari kita naik ke tingkat yang lebih menantang!',
                'required_fact_ids' => ['G21', 'G17', 'G11', 'G20'], // Benar, Acc > 90, Cepat, Tanpa Hint
                'actions'           => [$id::INCREASE_DIFF->value, $id::NEW_CHALLENGE->value],
            ],
            [
                'id'                => 'R02',
                'priority'          => 0,
                'name'              => 'Expert Mastery Milestone',
                'recommendation'    => 'Status Expert dan penguasaan materimu sangat solid. Siap untuk tantangan dadakan?',
                'required_fact_ids' => ['G21', 'G19', 'G15'], // Benar, Level Expert, Streak 5
                'actions'           => [$id::NEW_CHALLENGE->value, $id::STREAK_BONUS->value],
            ],

            // ==========================================================
            // KATEGORI 2: SUPPORT & SAFETY NET (PRIORITAS 1 - 5)
            // ==========================================================
            [
                'id'                => 'R03',
                'priority'          => 1,
                'name'              => 'Streak & Frustration Protection',
                'recommendation'    => 'Jangan biarkan semangatmu turun. Mari kita sesuaikan level soal agar kamu bisa kembali fokus dengan ritme yang lebih nyaman.',
                'required_fact_ids' => ['G22', 'G15', 'G12'], // Salah, Streak Tinggi/Lama, Lambat
                'actions'           => [$id::REDUCE_DIFF->value, $id::GIVE_HINT->value],
            ],
            [
                'id'                => 'R04',
                'priority'          => 2,
                'name'              => 'Deteksi Perilaku Instan (Guessing/Abuse)',
                'recommendation'    => 'Sistem mendeteksi jawaban yang terlalu cepat. Pastikan kamu membaca soal dan bantuan dengan teliti agar pemahamanmu maksimal.',
                'required_fact_ids' => ['G11', 'G23'], // Sangat Cepat + (Salah/Gunakan Hint)
                'actions'           => [$id::REDUCE_HINT->value],
            ],
            [
                'id'                => 'R05',
                'priority'          => 3,
                'name'              => 'Pencegahan Frustrasi (Struggling)',
                'recommendation'    => 'Soal ini memang menantang. Mari kita coba level yang lebih rendah sejenak agar pikiranmu segar kembali.',
                'required_fact_ids' => ['G22', 'G08', 'G12'], // Salah, Banyak Hint, Lambat
                'actions'           => [$id::REDUCE_DIFF->value, $id::GIVE_HINT->value],
            ],

            // ==========================================================
            // KATEGORI 3: REMEDIAL & CRISIS (PRIORITAS 6 - 9)
            // ==========================================================
            [
                'id'                => 'R06',
                'priority'          => 6,
                'name'              => 'Diagnosis Krisis Pembelajaran',
                'recommendation'    => 'Mari kita kembali ke konsep dasar. Penguatan pondasi sangat penting sebelum melangkah lebih jauh.',
                'required_fact_ids' => ['G22', 'G01', 'G05'], // Salah, Acc < 40, Tren Turun
                'deduced_fact_ids'  => ['V01'],
                'actions'           => [$id::REMEDIAL->value, $id::NOTIFY_TEACHER->value],
            ],
            [
                'id'                => 'R07',
                'priority'          => 7,
                'name'              => 'Intervensi Krisis Intensif',
                'recommendation'    => 'Konsep dasar butuh perhatian ekstra. Mari kita coba latihan yang paling sederhana.',
                'required_fact_ids' => ['V01', 'G08'], // Sudah Krisis + Masih Pakai Banyak Hint
                'actions'           => [$id::REMEDIAL_INTENSIVE->value],
            ],

            // ==========================================================
            // KATEGORI 4: STANDARD ADAPTATION (PRIORITAS 10 - 20)
            // ==========================================================
            [
                'id'                => 'R08',
                'priority'          => 10,
                'name'              => 'Peningkatan Kesulitan Standar',
                'recommendation'    => 'Kemampuanmu meningkat. Mari kita coba level yang sedikit lebih tinggi.',
                'required_fact_ids' => ['G21', 'G04', 'G07'], // Benar, Acc 80-90, Tren Naik
                'actions'           => [$id::INCREASE_DIFF->value],
            ],
            [
                'id'                => 'R09',
                'priority'          => 11,
                'name'              => 'Penurunan Kesulitan Standar',
                'recommendation'    => 'Mari kita sesuaikan level soal agar pas dengan ritme belajarmu saat ini.',
                'required_fact_ids' => ['G22', 'G02'], // Salah, Acc 40-80
                'actions'           => [$id::REDUCE_DIFF->value],
            ],
            [
                'id'                => 'R10',
                'priority'          => 15,
                'name'              => 'Apresiasi Ketelitian & Motivasi',
                'recommendation'    => 'Kerja bagus dalam menjawab secara hati-hati dan mandiri. Ini bonus untuk menyemangatimu!',
                'required_fact_ids' => ['G21', 'G12', 'G20'], // Benar, Lambat/Teliti, Tanpa Hint
                'actions'           => [$id::GIVE_HINT->value, $id::STREAK_BONUS->value],
            ],

            // ==========================================================
            // KATEGORI 5: FALLBACK (SAFETY NET)
            // ==========================================================
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
