<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttributeDefinition;
use App\Models\Formula;

class AttributeDefinitionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Define Formulas first (Embedded here for Single Source of Truth)
        $formulas = [
            'accuracy' => [
                'name' => 'Calculate Accuracy',
                'expression' => 'PERCENTAGE(correct_count, total_count)',
                'return_type' => 'float',
                'description' => 'Menghitung persentase akurasi jawaban benar',
                'scope' => 'material'
            ],
            'wrong_rate' => [
                'name' => 'Calculate Wrong Rate',
                'expression' => 'PERCENTAGE(wrong_count, total_count)',
                'return_type' => 'float',
                'description' => 'Menghitung persentase jawaban salah',
                'scope' => 'material'
            ],
            'is_high_performer' => [
                'name' => 'Is High Performer',
                'expression' => 'IF(accuracy >= 80, 1, 0)',
                'return_type' => 'boolean',
                'description' => 'Cek apakah mahasiswa high performer (accuracy >= 80%)',
                'scope' => 'material'
            ],
            'shop_points' => [
                'name' => 'Shop Points',
                'expression' => 'ROUND(xp * 0.5)',
                'return_type' => 'integer',
                'description' => 'Konversi XP menjadi shop points',
                'scope' => 'global'
            ]
        ];

        // Create Formulas
        foreach ($formulas as $key => $data) {
            Formula::updateOrCreate(
                ['key' => $key],
                array_merge($data, ['is_active' => true, 'sort_order' => 99])
            );
        }

        $attributes = [
            // Progression Attributes
            [
                'key' => 'xp',
                'label' => 'Experience Points',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'progression',
                'description' => 'Total experience points earned',
                'is_computed' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'points',
                'label' => 'Points',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'progression',
                'description' => 'Points for rewards/shop',
                'is_computed' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'coins',
                'label' => 'Coins',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'economy',
                'description' => 'Virtual currency for shop',
                'is_computed' => false,
                'sort_order' => 3
            ],
            
            // Gameplay Attributes
            [
                'key' => 'current_level',
                'label' => 'Current Difficulty Level',
                'type' => 'string',
                'default_value' => 'beginner',
                'category' => 'gameplay',
                'description' => 'Current difficulty level (beginner, medium, hard)',
                'is_computed' => false,
                'sort_order' => 10
            ],
            [
                'key' => 'current_streak',
                'label' => 'Current Streak',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Consecutive correct answers',
                'is_computed' => false,
                'sort_order' => 11
            ],
            [
                'key' => 'wrong_streak',
                'label' => 'Wrong Streak',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Consecutive wrong answers',
                'is_computed' => false,
                'sort_order' => 12
            ],
            [
                'key' => 'level_correct_count',
                'label' => 'Level Correct Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Correct answers in current level',
                'is_computed' => false,
                'sort_order' => 13
            ],
            [
                'key' => 'level_attempt_count',
                'label' => 'Level Attempt Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Total attempts in current level',
                'is_computed' => false,
                'sort_order' => 14
            ],
            [
                'key' => 'hints_used',
                'label' => 'Hints Used',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Number of hints used',
                'is_computed' => false,
                'sort_order' => 15
            ],
            [
                'key' => 'hints_available',
                'label' => 'Hints Available',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Number of hints available to use',
                'is_computed' => false,
                'sort_order' => 16
            ],
            [
                'key' => 'retry_count',
                'label' => 'Retry Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Number of retries on current level',
                'is_computed' => false,
                'sort_order' => 17
            ],
            [
                'key' => 'total_questions_answered',
                'label' => 'Total Questions Answered',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Total number of questions answered in current material',
                'is_computed' => false,
                'sort_order' => 18
            ],
            [
                'key' => 'badges',
                'label' => 'Badges',
                'type' => 'string',
                'default_value' => '',
                'category' => 'progression',
                'description' => 'Comma-separated list of earned badges',
                'is_computed' => false,
                'sort_order' => 19
            ],
            [
                'key' => 'show_material_redirect',
                'label' => 'Show Material Redirect',
                'type' => 'boolean',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Flag to show material redirect prompt',
                'is_computed' => false,
                'sort_order' => 20
            ],
            [
                'key' => 'correct_count',
                'label' => 'Correct Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Total correct answers',
                'is_computed' => false,
                'sort_order' => 21
            ],
            [
                'key' => 'wrong_count',
                'label' => 'Wrong Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Total wrong answers',
                'is_computed' => false,
                'sort_order' => 22
            ],
            [
                'key' => 'total_count',
                'label' => 'Total Count',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'gameplay',
                'description' => 'Total attempts',
                'is_computed' => false,
                'sort_order' => 23
            ],
            
            // Computed Attributes (Linked to Formulas created above)
            [
                'key' => 'accuracy',
                'label' => 'Accuracy (%)',
                'type' => 'float',
                'default_value' => '0',
                'category' => 'computed', // Changed to computed category
                'description' => 'Accuracy percentage',
                'is_computed' => true,
                'formula_key' => 'accuracy',
                'sort_order' => 30
            ],
            [
                'key' => 'wrong_rate',
                'label' => 'Wrong Rate (%)',
                'type' => 'float',
                'default_value' => '0',
                'category' => 'computed',
                'description' => 'Wrong answer percentage',
                'is_computed' => true,
                'formula_key' => 'wrong_rate',
                'sort_order' => 31
            ],
            [
                'key' => 'is_high_performer',
                'label' => 'Is High Performer',
                'type' => 'boolean',
                'default_value' => '0',
                'category' => 'computed',
                'description' => 'High performer status',
                'is_computed' => true,
                'formula_key' => 'is_high_performer',
                'sort_order' => 32
            ],
            [
                'key' => 'shop_points', // Added shop points computed attribute
                'label' => 'Shop Points',
                'type' => 'integer',
                'default_value' => '0',
                'category' => 'computed',
                'description' => 'Shop points from XP conversion',
                'is_computed' => true,
                'formula_key' => 'shop_points',
                'sort_order' => 33
            ]
        ];
        
        foreach ($attributes as $attrData) {
            // Get formula_id if this is a computed attribute
            $formulaId = null;
            if (isset($attrData['formula_key'])) {
                $formula = Formula::where('key', $attrData['formula_key'])->first();
                $formulaId = $formula ? $formula->id : null;
                // unset($attrData['formula_key']); // Keep optional for reference or unset
            }
            
            // Remove formula_key from data passed to create
            if (isset($attrData['formula_key'])) unset($attrData['formula_key']);

            AttributeDefinition::updateOrCreate(
                ['key' => $attrData['key']],
                array_merge($attrData, ['formula_id' => $formulaId, 'is_active' => true])
            );
        }
    }
}
