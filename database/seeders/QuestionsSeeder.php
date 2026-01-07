<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // ==================== Material 1: Introduction to OOP ====================
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
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 1,
                'question_text' => 'OOP helps in organizing code by grouping related data and _____ together.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 1,
                'question_text' => 'What does OOP stand for?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 1,
                'question_text' => 'Which programming languages support OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 1,
                'question_text' => 'The four main principles of OOP are: Encapsulation, Abstraction, Inheritance, and _____.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            
            // ==================== Material 2: Classes and Objects ====================
            [
                'material_id' => 2,
                'question_text' => 'What is a class in OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'What is an object in OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'A class is a _____ for creating objects.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'Arrange the steps to create and use an object: [zone], [zone], [zone]',
                'question_type' => 'drag_and_drop',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'Which keyword is used to create a new object in Java?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'Can a class have multiple objects?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'What is a constructor?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'The _____ method is called automatically when an object is created.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 2,
                'question_text' => 'What are attributes in a class?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            
            // ==================== Material 3: Inheritance ====================
            [
                'material_id' => 3,
                'question_text' => 'What keyword is used in Java to inherit from a parent class?',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'What is inheritance in OOP?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'A class that inherits from another class is called a _____ class.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'What is the parent class also known as?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'Can a subclass access private members of its superclass?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'Arrange inheritance hierarchy: [zone], [zone], [zone]',
                'question_type' => 'drag_and_drop',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'What type of inheritance does Java NOT support directly?',
                'question_type' => 'radio_button',
                'difficulty' => 'hard',
                'created_by' => 2,
            ],
            [
                'material_id' => 3,
                'question_text' => 'The _____ keyword is used to call a parent class constructor.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            
            // ==================== Material 4: Polymorphism ====================
            [
                'material_id' => 4,
                'question_text' => 'Which type of polymorphism is resolved at compile time?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'What is polymorphism?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'Method _____ allows multiple methods with the same name but different parameters.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'What is method overriding?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'Which annotation is used for method overriding in Java?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'Polymorphism allows objects of different classes to be treated as objects of a common _____ class.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'hard',
                'created_by' => 2,
            ],
            [
                'material_id' => 4,
                'question_text' => 'Arrange polymorphism concepts by execution time: [zone], [zone]',
                'question_type' => 'drag_and_drop',
                'difficulty' => 'hard',
                'created_by' => 2,
            ],
            
            // ==================== Material 5: Encapsulation ====================
            [
                'material_id' => 5,
                'question_text' => 'What is the main benefit of encapsulation?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'What is encapsulation?',
                'question_type' => 'radio_button',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'The _____ access modifier makes a variable accessible only within its own class.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'beginner',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'What are getter and setter methods used for?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'Which access modifier provides the most restriction?',
                'question_type' => 'radio_button',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'Arrange access modifiers from most to least restrictive: [zone], [zone], [zone], [zone]',
                'question_type' => 'drag_and_drop',
                'difficulty' => 'hard',
                'created_by' => 2,
            ],
            [
                'material_id' => 5,
                'question_text' => 'Encapsulation is also known as data _____.',
                'question_type' => 'fill_in_the_blank',
                'difficulty' => 'medium',
                'created_by' => 2,
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
