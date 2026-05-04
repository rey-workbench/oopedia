<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MslqQuestion;
use Illuminate\Database\Seeder;

/**
 * MslqOopediaSeeder.
 * Curated 12 MSLQ items for OOPedia Pre/Post-Test.
 * Focused on Motivation and Learning Strategies that drive the Adaptive Engine.
 */
final class MslqOopediaSeeder extends Seeder
{
    public function run(): void
    {
        $selectedItems = [
            // Motivation - Self Efficacy (Key for difficulty adaptation)
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 1, 'Saya yakin saya dapat memahami konsep yang paling sulit yang diajarkan oleh dosen di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 2, 'Saya yakin saya dapat memahami materi yang paling rumit yang disampaikan di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 3, 'Saya yakin saya dapat mengerjakan tes di mata kuliah ini dengan baik.'],

            // Motivation - Intrinsic Goal Orientation (Key for curiosity-driven paths)
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 4, 'Di kelas seperti ini, saya lebih suka materi yang membangkitkan rasa ingin tahu saya meskipun sulit untuk dipelajari.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 5, 'Hal yang paling memuaskan bagi saya di kelas ini adalah mencoba untuk memahami materi sedalam mungkin.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 6, 'Di kelas seperti ini, saya lebih memilih materi kuliah yang benar-benar menantang sehingga saya dapat mempelajari hal-hal baru.'],

            // Learning Strategy - Metacognitive Self-Regulation (Key for scaffolding)
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 7, 'Saya mencoba menentukan materi mana yang belum saya pahami dengan baik.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 8, 'Bila saya belajar, saya mencoba memutuskan konsep mana yang paling tidak saya pahami dengan baik.'],
            ['mslq_metacognitive_self_regulation', true, 'learning_strategy', 9, 'Bila saya sedang belajar, saya sering memikirkan hal-hal lain dan tidak benar-benar membaca apa yang saya baca.'],

            // Learning Strategy - Effort Regulation (Key for persistence/hints)
            ['mslq_effort_regulation', false, 'learning_strategy', 10, 'Bila sebuah topik terasa sulit, saya tetap berusaha mencoba mempelajarinya terus.'],
            ['mslq_effort_regulation', false, 'learning_strategy', 11, 'Saya bekerja keras untuk mendapatkan hasil yang baik meskipun saya tidak menyukai materi yang dipelajari.'],
            ['mslq_effort_regulation', true, 'learning_strategy', 12, 'Bila materi kuliah terasa sulit, saya menyerah atau hanya mengerjakan bagian yang mudah saja.'],
        ];

        foreach ($selectedItems as $item) {
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
