<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MslqQuestion;
use Illuminate\Database\Seeder;

/**
 * MslqOopediaSeeder.
 * Curated 15 MSLQ items for OOPedia Pre/Post-Test.
 */
final class MslqOopediaSeeder extends Seeder
{
    public function run(): void
    {
        $selectedItems = [
            // Motivation - Self Efficacy
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 1, 'Saya yakin saya dapat memahami konsep yang paling sulit yang diajarkan oleh dosen di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 2, 'Saya yakin saya dapat mempelajari materi dasar yang diajarkan di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 3, 'Saya yakin saya dapat memahami materi yang paling rumit yang disampaikan oleh dosen di mata kuliah ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 4, 'Saya yakin saya dapat menguasai keahlian yang diajarkan di kelas ini.'],
            ['mslq_self_efficacy_for_learning_performance', false, 'motivation', 5, 'Saya yakin saya dapat mengerjakan tes di mata kuliah ini dengan baik.'],

            // Motivation - Intrinsic Goal Orientation
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 6, 'Di kelas seperti ini, saya lebih suka materi yang membangkitkan rasa ingin tahu saya meskipun sulit untuk dipelajari.'],
            ['mslq_intrinsic_goal_orientation', false, 'motivation', 7, 'Hal yang paling memuaskan bagi saya di kelas ini adalah mencoba untuk memahami materi sedalam mungkin.'],

            // Motivation - Task Value
            ['mslq_task_value', false, 'motivation', 8, 'Saya sangat menyukai materi kuliah di mata kuliah ini.'],
            ['mslq_task_value', false, 'motivation', 9, 'Saya pikir materi kuliah di kelas ini berguna bagi saya untuk dipelajari.'],
            ['mslq_task_value', false, 'motivation', 10, 'Memahami materi kuliah ini sangat penting bagi saya.'],

            // Learning Strategy - Metacognitive Self-Regulation
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 11, 'Saya mencoba menentukan materi mana yang belum saya pahami dengan baik.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 12, 'Bila saya sedang belajar, saya menetapkan tujuan untuk diri saya sendiri guna mengarahkan kegiatan belajar saya.'],
            ['mslq_metacognitive_self_regulation', false, 'learning_strategy', 13, 'Bila saya belajar, saya mencoba memutuskan konsep mana yang paling tidak saya pahami dengan baik.'],

            // Learning Strategy - Critical Thinking
            ['mslq_critical_thinking', false, 'learning_strategy', 14, 'Seringkali saya bertanya pada diri saya sendiri tentang hal-hal yang saya baca di kelas ini untuk menentukan apakah hal-hal tersebut meyakinkan.'],
            ['mslq_critical_thinking', false, 'learning_strategy', 15, 'Bila saya sedang belajar, saya mencoba memikirkan alternatif cara untuk memecahkan sebuah masalah.'],
        ];

        foreach ($selectedItems as $item) {
            MslqQuestion::updateOrCreate(
                ['order' => $item[3]],
                [
                    'scale'      => $item[0],
                    'is_reverse' => $item[1],
                    'category'   => $item[2],
                    'text'       => $item[4],
                ],
            );
        }
    }
}
