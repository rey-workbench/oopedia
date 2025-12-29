<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UeqSurvey;

class UeqSurveysSeeder extends Seeder
{
    public function run(): void
    {
        $surveys = [
            [
                'user_id' => 4, // Mahasiswa
                'nim' => '12345678',
                'class' => 'TI-3A',
                'annoying_enjoyable' => 6,
                'not_understandable_understandable' => 5,
                'creative_dull' => 4,
                'easy_difficult' => 5,
                'valuable_inferior' => 6,
                'boring_exciting' => 5,
                'not_interesting_interesting' => 6,
                'unpredictable_predictable' => 4,
                'fast_slow' => 5,
                'inventive_conventional' => 5,
                'obstructive_supportive' => 6,
                'good_bad' => 6,
                'complicated_easy' => 5,
                'unlikable_pleasing' => 6,
                'usual_leading_edge' => 4,
                'unpleasant_pleasant' => 6,
                'secure_not_secure' => 5,
                'motivating_demotivating' => 6,
                'meets_expectations_does_not_meet' => 5,
                'inefficient_efficient' => 5,
                'clear_confusing' => 6,
                'impractical_practical' => 5,
                'organized_cluttered' => 6,
                'attractive_unattractive' => 5,
                'friendly_unfriendly' => 6,
                'conservative_innovative' => 4,
                'comments' => 'Good learning platform',
                'suggestions' => 'Add more interactive examples',
            ],
            [
                'user_id' => 5, // Another mahasiswa
                'nim' => '87654321',
                'class' => 'TI-3B',
                'annoying_enjoyable' => 5,
                'not_understandable_understandable' => 6,
                'creative_dull' => 5,
                'easy_difficult' => 4,
                'valuable_inferior' => 5,
                'boring_exciting' => 4,
                'not_interesting_interesting' => 5,
                'unpredictable_predictable' => 5,
                'fast_slow' => 4,
                'inventive_conventional' => 4,
                'obstructive_supportive' => 5,
                'good_bad' => 5,
                'complicated_easy' => 4,
                'unlikable_pleasing' => 5,
                'usual_leading_edge' => 5,
                'unpleasant_pleasant' => 5,
                'secure_not_secure' => 6,
                'motivating_demotivating' => 5,
                'meets_expectations_does_not_meet' => 6,
                'inefficient_efficient' => 4,
                'clear_confusing' => 5,
                'impractical_practical' => 6,
                'organized_cluttered' => 5,
                'attractive_unattractive' => 6,
                'friendly_unfriendly' => 5,
                'conservative_innovative' => 5,
                'comments' => 'Helpful for studying OOP',
                'suggestions' => 'More practice questions',
            ],
        ];

        foreach ($surveys as $survey) {
            UeqSurvey::create($survey);
        }
    }
}
