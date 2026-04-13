<?php

namespace Database\Seeders;

use App\Models\MslqQuestion;
use Illuminate\Database\Seeder;

class MslqQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Full 81 items mapping...
        $fullData = [
            // [scale, is_reverse, category, order, text]
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 1, 'Dalam kelas seperti ini, saya lebih suka materi kuliah yang benar-benar menantang saya sehingga saya dapat mempelajari hal-hal baru.'],
            ['mslq_control_of_learning_beliefs', false, 'motivation', 2, 'Jika saya belajar dengan cara yang tepat, saya akan dapat mempelajari materi di mata kuliah ini.'],
            ['mslq_test_anxiety', false, 'motivation', 3, 'Ketika saya menempuh tes saya berpikir tentang betapa buruknya hasil yang saya peroleh dibandingkan dengan mahasiswa lain.'],
            ['mslq_task_value', false, 'motivation', 4, 'Saya pikir saya akan mampu menggunakan apa yang saya pelajari dari mata kuliah ini di mata kuliah lain.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 5, 'Saya percaya saya akan mendapatkan nilai yang sangat baik di kelas ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 6, 'Saya yakin saya dapat memahami konsep yang paling sulit yang diajarkan oleh dosen di mata kuliah ini.'],
            ['mslq_extrinsic_goal_orientation', false, 'motivation', 7, 'Mendapatkan nilai bagus di kelas ini adalah hal yang paling memuaskan bagi saya saat ini.'],
            ['mslq_test_anxiety', false, 'motivation', 8, 'Bila saya menempuh tes saya memikirkan butir-butir soal pada bagian lain dari tes yang tidak dapat saya jawab.'],
            ['mslq_control_of_learning_beliefs', false, 'motivation', 9, 'Adalah kesalahan saya sendiri jika saya tidak mempelajari materi di mata kuliah ini.'],
            ['mslq_task_value', false, 'motivation', 10, 'Sangat penting bagi saya untuk mempelajari materi kuliah di kelas ini.'],
            ['mslq_extrinsic_goal_orientation', false, 'motivation', 11, 'Hal yang paling ingin saya dapatkan di kelas ini adalah nilai yang bagus.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 12, 'Saya yakin saya dapat mempelajari materi dasar yang diajarkan di mata kuliah ini.'],
            ['mslq_extrinsic_goal_orientation', false, 'motivation', 13, 'Jika saya dapat memilih, saya lebih suka materi kuliah yang bisa membantu saya mendapatkan nilai bagus.'],
            ['mslq_test_anxiety', false, 'motivation', 14, 'Bila saya menempuh tes saya merasa gelisah dan tidak tenang.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 15, 'Saya yakin saya dapat memahami materi yang paling rumit yang disampaikan oleh dosen di mata kuliah ini.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 16, 'Di kelas seperti ini, saya lebih suka materi yang membangkitkan rasa ingin tahu saya meskipun sulit untuk dipelajari.'],
            ['mslq_task_value', false, 'motivation', 17, 'Saya sangat menyukai materi kuliah di mata kuliah ini.'],
            ['mslq_control_of_learning_beliefs', false, 'motivation', 18, 'Jika saya berusaha cukup keras, saya akan mampu memahami materi kuliah tersebut.'],
            ['mslq_test_anxiety', false, 'motivation', 19, 'Saya merasa jantung saya berdegup kencang bila saya menempuh tes.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 20, 'Saya yakin saya dapat mencapai hasil yang memuaskan pada tugas-tugas di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 21, 'Saya berharap saya dapat mengerjakan tugas dengan baik di kelas ini.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 22, 'Hal yang paling memuaskan bagi saya di kelas ini adalah mencoba untuk memahami materi sedalam mungkin.'],
            ['mslq_task_value', false, 'motivation', 23, 'Saya pikir materi kuliah di kelas ini berguna bagi saya untuk dipelajari.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 24, 'Bila saya mempunyai kesempatan, saya akan memilih materi kuliah yang dapat saya pelajari meskipun materi tersebut tidak menjamin nilai yang bagus.'],
            ['mslq_control_of_learning_beliefs', false, 'motivation', 25, 'Jika saya tidak memahami materi kuliah tersebut, hal itu karena saya tidak berusaha cukup keras.'],
            ['mslq_task_value', false, 'motivation', 26, 'Saya menyukai materi kuliah di kelas ini.'],
            ['mslq_task_value', false, 'motivation', 27, 'Memahami materi kuliah ini sangat penting bagi saya.'],
            ['mslq_test_anxiety', false, 'motivation', 28, 'Saya merasa tidak nyaman dan bingung bila saya mengerjakan tes.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 29, 'Saya yakin saya dapat menguasai keahlian yang diajarkan di kelas ini.'],
            ['mslq_extrinsic_goal_orientation', false, 'motivation', 30, 'Saya ingin mendapatkan nilai yang lebih baik di kelas ini daripada mahasiswa lain.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 31, 'Saya yakin saya dapat mengerjakan tes di mata kuliah ini dengan baik.'],

            ['mslq_organization', false, 'learning_strategy', 32, 'Bila saya belajar untuk mata kuliah ini, saya mencari hubungan antara materi yang saya baca dengan konsep yang saya pelajari lewat ceramah dosen.'],
            ['mslq_metacognitive_self_regulation', true, 'learning_strategy', 33, 'Bila saya membaca bacaan untuk mata kuliah ini, saya sering merasa bahwa saya telah melaluinya tanpa mengetahui apa yang dimaksudkan.'],
            ['mslq_peer_learning', false, 'learning_strategy', 34, 'Bila saya belajar untuk mata kuliah ini, saya mendiskusikan materi dengan mahasiswa lain untuk membantu saya dalam belajar.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 35, 'Saya biasanya belajar di suatu tempat di mana saya dapat berkonsentrasi.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 36, 'Bila saya membaca bacaan untuk mata kuliah ini, saya mencoba menghubungkannya dengan apa yang sudah saya ketahui.'],
            ['mslq_effort_regulation', false, 'learning_strategy', 37, 'Saya sering merasa sangat malas atau bosan bila saya belajar untuk mata kuliah ini sehingga saya berhenti sebelum mengerjakan apa yang saya rencanakan.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 38, 'Seringkali saya bertanya pada diri saya sendiri tentang hal-hal yang saya baca di kelas ini untuk menentukan apakah hal-hal tersebut meyakinkan.'],
            ['mslq_rehearsal', false, 'learning_strategy', 39, 'Saya menghafalkan poin-poin penting untuk membantu saya mempelajari materi di mata kuliah ini.'],
            ['mslq_help_seeking', true, 'learning_strategy', 40, 'Meskipun materi kuliah tersebut sulit, saya biasanya mengerjakannya sendiri tanpa meminta bantuan siapapun.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 41, 'Bila saya belajar untuk mata kuliah ini, saya mencoba menguraikan materi tersebut untuk membantu saya mengaturnya.'],
            ['mslq_organization', false, 'learning_strategy', 42, 'Bila saya belajar, saya membaca catatan saya dan materi-materi teks berulang-ulang.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 43, 'Saya mencoba menghubungkan materi di kelas ini dengan apa yang saya pelajari di kelas lain.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 44, 'Bila saya belajar untuk sebuah tes, saya mencoba mengumpulkan semua informasi dari kuliah dosen dan dari buku teks.'],
            ['mslq_peer_learning', false, 'learning_strategy', 45, 'Saya mempunyai rencana atau jadwal untuk belajar.'],
            ['mslq_rehearsal', false, 'learning_strategy', 46, 'Bila saya belajar untuk mata kuliah ini, saya menulis ringkasan singkat dari poin-poin utama dari teks dan catatan saya.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 47, 'Seringkali saya mencoba menjelaskan materi di mata kuliah ini kepada teman kuliah saya.'],
            ['mslq_effort_regulation', true, 'learning_strategy', 48, 'Saya bekerja keras untuk mendapatkan hasil yang baik meskipun saya tidak menyukai materi yang dipelajari.'],
            ['mslq_organization', false, 'learning_strategy', 49, 'Saya membuat bagan, diagram, atau tabel untuk membantu saya mengatur materi kuliah.'],
            ['mslq_peer_learning', false, 'learning_strategy', 50, 'Saya mencoba menghubungkan materi dengan apa yang sudah saya ketahui dari kuliah dosen dan buku teks.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 51, 'Bila saya belajar untuk mata kuliah ini, saya sering menyisihkan waktu untuk mendiskusikan materi kuliah dengan teman sekelas saya.'],
            ['mslq_time_study_environment_management', true, 'learning_strategy', 52, 'Bila materi kuliah membingungkan, saya mencoba mengubah cara mengerjakan tugas tersebut.'],
            ['mslq_elaboration', false, 'learning_strategy', 53, 'Saya membaca catatan kuliah saya dan membuat poin-poin utama tapi tidak terperinci.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 54, 'Bila saya sedang belajar, saya sering memikirkan hal-hal lain dan tidak benar-benar membaca apa yang saya baca.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 55, 'Saya mencoba menentukan materi mana yang belum saya pahami dengan baik.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 56, 'Saya membuat catatan dari bacaan saya sebagai sarana untuk belajar.'],
            ['mslq_metacognitive_self_regulation', true, 'learning_strategy', 57, 'Bila saya sedang belajar, saya mencoba mengingat kembali apa yang telah saya baca untuk memastikan bahwa saya telah memahaminya.'],
            ['mslq_help_seeking', false, 'learning_strategy', 58, 'Saya suka belajar di rumah daripada di kampus.'],
            ['mslq_rehearsal', false, 'learning_strategy', 59, 'Bila saya belajar untuk mata kuliah ini, saya mencoba mengidentifikasi bagian-bagian yang tidak saya pahami dengan baik.'],
            ['mslq_effort_regulation', false, 'learning_strategy', 60, 'Bila materi kuliah terasa sulit, saya menyerah atau hanya mengerjakan bagian yang mudah saja.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 61, 'Bila saya sedang belajar, saya menetapkan tujuan untuk diri saya sendiri guna mengarahkan kegiatan belajar saya.'],
            ['mslq_elaboration', false, 'learning_strategy', 62, 'Saya membaca tugas-tugas bacaan saya dan catatan saya berulang-ulang.'],
            ['mslq_organization', false, 'learning_strategy', 63, 'Bila saya sedang belajar, saya mencoba menerapkan materi dari kuliah dosen ke tugas-tugas lain seperti bacaan atau diskusi.'],
            ['mslq_elaboration', false, 'learning_strategy', 64, 'Bila saya belajar untuk mata kuliah ini, saya membaca kembali catatan saya dan membuat garis besar dari konsep-konsep penting.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 65, 'Bila saya tidak dapat memahami sesuatu, saya meminta dosen untuk menjelaskan lebih jauh.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 66, 'Seringkali saya merasa bosan dengan mata kuliah ini sehingga saya berhenti belajar sebelum saya selesai.'],
            ['mslq_elaboration', false, 'learning_strategy', 67, 'Bila saya sedang belajar, saya mencoba memikirkan alternatif cara untuk memecahkan sebuah masalah.'],
            ['mslq_help_seeking', false, 'learning_strategy', 68, 'Saya menghadiri kuliah dosen secara teratur.'],
            ['mslq_elaboration', false, 'learning_strategy', 69, 'Bila saya sedang belajar, saya mencoba menguji diri saya sendiri untuk memastikan bahwa saya sudah memahami materi materi tersebut.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 70, 'Bila saya tidak dapat memahami materi di mata kuliah ini, saya meminta bantuan mahasiswa lain.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 71, 'Saya mencoba mengikuti jadwal belajar saya.'],
            ['mslq_rehearsal', false, 'learning_strategy', 72, 'Bila saya belajar, saya mencoba memadukan informasi dari berbagai sumber, seperti dari dosen, bacaan, dan diskusi.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 73, 'Saya mengatur waktu belajar saya agar saya dapat menyelesaikan tugas-tugas tepat pada waktunya.'],
            ['mslq_effort_regulation', true, 'learning_strategy', 74, 'Bila sebuah topik terasa sulit, saya tetap berusaha mencoba mempelajarinya terus.'],
            ['mslq_help_seeking', false, 'learning_strategy', 75, 'Bila saya belajar, saya mencoba memutuskan konsep mana yang paling tidak saya pahami dengan baik.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 76, 'Bila saya belajar, saya membaca kembali catatan saya dan membuat garis besar.'],
            ['mslq_time_study_environment_management', true, 'learning_strategy', 77, 'Bila saya tidak dapat memahami suatu konsep, saya mencoba mencari informasi lebih lanjut dari buku lain atau sumber tambahan.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 78, 'Saya mencoba menghubungkan apa yang saya baca di mata kuliah ini dengan materi kuliah lain.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 79, 'Bila saya tidak memahami materi kuliah ini, saya mencari bantuan mahasiswa lain.'],
            ['mslq_time_study_environment_management', false, 'learning_strategy', 80, 'Saya berusaha keras agar tetap fokus saat mengerjakan tugas-tugas kuliah.'],
            ['mslq_elaboration', false, 'learning_strategy', 81, 'Bila saya belajar, saya mencoba memikirkan bagaimana materi tersebut berkaitan dengan apa yang sudah saya ketahui.'],
        ];

        foreach ($fullData as $item) {
            MslqQuestion::create([
                'scale'      => $item[0],
                'is_reverse' => $item[1],
                'category'   => $item[2],
                'order'      => $item[3],
                'text'       => $item[4],
            ]);
        }
    }
}
