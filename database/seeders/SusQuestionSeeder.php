<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SusQuestion;
use Illuminate\Database\Seeder;

/**
 * SusQuestionSeeder.
 * Standardized 10 SUS items in Indonesian.
 */
final class SusQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['order' => 1,  'is_reverse' => false, 'text' => 'Saya rasa saya akan sering menggunakan sistem ini.'],
            ['order' => 2,  'is_reverse' => true,  'text' => 'Saya merasa sistem ini rumit untuk digunakan, padahal seharusnya bisa lebih sederhana.'],
            ['order' => 3,  'is_reverse' => false, 'text' => 'Saya rasa sistem ini mudah digunakan.'],
            ['order' => 4,  'is_reverse' => true,  'text' => 'Saya rasa saya butuh bantuan orang teknis untuk dapat menggunakan sistem ini.'],
            ['order' => 5,  'is_reverse' => false, 'text' => 'Saya merasa fitur-fitur dalam sistem ini terintegrasi dengan baik.'],
            ['order' => 6,  'is_reverse' => true,  'text' => 'Saya merasa ada banyak hal yang tidak konsisten pada sistem ini.'],
            ['order' => 7,  'is_reverse' => false, 'text' => 'Saya rasa orang lain akan belajar menggunakan sistem ini dengan sangat cepat.'],
            ['order' => 8,  'is_reverse' => true,  'text' => 'Saya merasa sistem ini sangat membingungkan saat digunakan.'],
            ['order' => 9,  'is_reverse' => false, 'text' => 'Saya merasa sangat percaya diri saat menggunakan sistem ini.'],
            ['order' => 10, 'is_reverse' => true,  'text' => 'Saya harus belajar banyak hal terlebih dahulu sebelum saya dapat mulai menggunakan sistem ini.'],
        ];

        foreach ($questions as $q) {
            SusQuestion::updateOrCreate(
                ['order' => $q['order']],
                [
                    'text'       => $q['text'],
                    'is_reverse' => $q['is_reverse'],
                ],
            );
        }
    }
}
