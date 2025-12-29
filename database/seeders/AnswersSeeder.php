<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;

class AnswersSeeder extends Seeder
{
    public function run(): void
    {
        $answers = [
            // Question 1: What is Object-Oriented Programming? (radio_button)
            [
                'question_id' => 1,
                'is_correct' => true,
                'answer_text' => 'A programming paradigm based on objects',
                'explanation' => 'Correct! OOP is based on the concept of objects.',
            ],
            [
                'question_id' => 1,
                'is_correct' => false,
                'answer_text' => 'A type of database',
                'explanation' => 'Incorrect. OOP is a programming paradigm, not a database type.',
            ],
            [
                'question_id' => 1,
                'is_correct' => false,
                'answer_text' => 'A markup language',
                'explanation' => 'Incorrect. OOP is a programming paradigm.',
            ],

            // Question 2: Which is NOT a principle of OOP? (radio_button)
            [
                'question_id' => 2,
                'is_correct' => true,
                'answer_text' => 'Globalization',
                'explanation' => 'Correct! Globalization is not an OOP principle.',
            ],
            [
                'question_id' => 2,
                'is_correct' => false,
                'answer_text' => 'Encapsulation',
                'explanation' => 'Incorrect. Encapsulation is a core OOP principle.',
            ],
            [
                'question_id' => 2,
                'is_correct' => false,
                'answer_text' => 'Inheritance',
                'explanation' => 'Incorrect. Inheritance is a core OOP principle.',
            ],

            // Question 3: What is a class in OOP? (radio_button)
            [
                'question_id' => 3,
                'is_correct' => true,
                'answer_text' => 'A blueprint for creating objects',
                'explanation' => 'Correct! A class defines the structure and behavior.',
            ],
            [
                'question_id' => 3,
                'is_correct' => false,
                'answer_text' => 'An instance of an object',
                'explanation' => 'Incorrect. An object is an instance of a class.',
            ],

            // Question 4: Arrange steps (drag_and_drop)
            [
                'question_id' => 4,
                'is_correct' => true,
                'drag_source' => '1. Define class',
                'drag_target' => '1',
            ],
            [
                'question_id' => 4,
                'is_correct' => true,
                'drag_source' => '2. Instantiate object',
                'drag_target' => '2',
            ],
            [
                'question_id' => 4,
                'is_correct' => true,
                'drag_source' => '3. Use object',
                'drag_target' => '3',
            ],

            // Question 5: Keyword for inheritance (fill_in_the_blank)
            [
                'question_id' => 5,
                'is_correct' => true,
                'answer_text' => 'extends',
                'blank_position' => 1,
                'explanation' => 'Correct! In Java, extends is used for inheritance.',
            ],

            // Question 6: Compile time polymorphism (radio_button)
            [
                'question_id' => 6,
                'is_correct' => true,
                'answer_text' => 'Method Overloading',
                'explanation' => 'Correct! Method overloading is compile-time polymorphism.',
            ],
            [
                'question_id' => 6,
                'is_correct' => false,
                'answer_text' => 'Method Overriding',
                'explanation' => 'Incorrect. Method overriding is runtime polymorphism.',
            ],

            // Question 7: Benefit of encapsulation (radio_button)
            [
                'question_id' => 7,
                'is_correct' => true,
                'answer_text' => 'Data hiding and security',
                'explanation' => 'Correct! Encapsulation provides data security.',
            ],
            [
                'question_id' => 7,
                'is_correct' => false,
                'answer_text' => 'Faster execution',
                'explanation' => 'Incorrect. Encapsulation is about data protection, not speed.',
            ],
        ];

        foreach ($answers as $answer) {
            Answer::create($answer);
        }
    }
}
