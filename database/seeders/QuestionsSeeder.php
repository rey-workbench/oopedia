<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // Material 1: Introduction to OOP
            [
                'material_id' => 1,
                'question_text' => 'What is Object-Oriented Programming?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 1,
                'question_text' => 'Which of the following is NOT a principle of OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            // Material 2: Classes and Objects
            [
                'material_id' => 2,
                'question_text' => 'What is a class in OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'Arrange the steps to create an object: 1. Define class, 2. Instantiate object, 3. Use object',
                'question_type' => 'drag_and_drop',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            // Material 3: Inheritance
            [
                'material_id' => 3,
                'question_text' => 'What keyword is used in Java to inherit from a parent class?',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            // Material 4: Polymorphism
            [
                'material_id' => 4,
                'question_text' => 'Which type of polymorphism is resolved at compile time?',
                'question_type' => 'radio_button',
                'difficulty' => 'hard',
                'created_by' => 2,
            ],
            // Material 5: Encapsulation
            [
                'material_id' => 5,
                'question_text' => 'What is the main benefit of encapsulation?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
