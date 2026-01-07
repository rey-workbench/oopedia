<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;

class AnswersSeeder extends Seeder
{
    public function run(): void
    {
        $answers = [
            // ==================== Question 1: What is OOP? (radio_button) ====================
            ['question_id' => 1, 'is_correct' => true, 'answer_text' => 'A programming paradigm based on objects', 'explanation' => 'Correct! OOP is based on the concept of objects that contain data and methods.'],
            ['question_id' => 1, 'is_correct' => false, 'answer_text' => 'A type of database', 'explanation' => 'Incorrect. OOP is a programming paradigm, not a database type.'],
            ['question_id' => 1, 'is_correct' => false, 'answer_text' => 'A markup language', 'explanation' => 'Incorrect. OOP is not a markup language like HTML or XML.'],

            // ==================== Question 2: NOT a principle of OOP (radio_button) ====================
            ['question_id' => 2, 'is_correct' => true, 'answer_text' => 'Globalization', 'explanation' => 'Correct! Globalization is not an OOP principle.'],
            ['question_id' => 2, 'is_correct' => false, 'answer_text' => 'Encapsulation', 'explanation' => 'Incorrect. Encapsulation is a core OOP principle.'],
            ['question_id' => 2, 'is_correct' => false, 'answer_text' => 'Inheritance', 'explanation' => 'Incorrect. Inheritance is a core OOP principle.'],
            ['question_id' => 2, 'is_correct' => false, 'answer_text' => 'Polymorphism', 'explanation' => 'Incorrect. Polymorphism is a core OOP principle.'],

            // ==================== Question 3: OOP groups data and ___ (fill_in_the_blank) ====================
            ['question_id' => 3, 'is_correct' => true, 'answer_text' => 'functions', 'blank_position' => 1, 'explanation' => 'Correct! OOP groups data and functions (methods) together.'],
            ['question_id' => 3, 'is_correct' => true, 'answer_text' => 'methods', 'blank_position' => 1, 'explanation' => 'Correct! Functions are called methods in OOP.'],
            ['question_id' => 3, 'is_correct' => true, 'answer_text' => 'behavior', 'blank_position' => 1, 'explanation' => 'Correct! Behavior (methods) is grouped with data.'],

            // ==================== Question 4: What does OOP stand for? (radio_button) ====================
            ['question_id' => 4, 'is_correct' => true, 'answer_text' => 'Object-Oriented Programming', 'explanation' => 'Correct!'],
            ['question_id' => 4, 'is_correct' => false, 'answer_text' => 'Object-Oriented Process', 'explanation' => 'Incorrect.'],
            ['question_id' => 4, 'is_correct' => false, 'answer_text' => 'Organized Object Programming', 'explanation' => 'Incorrect.'],

            // ==================== Question 5: Languages that support OOP (radio_button) ====================
            ['question_id' => 5, 'is_correct' => true, 'answer_text' => 'Java, C++, Python', 'explanation' => 'Correct! All these languages support OOP.'],
            ['question_id' => 5, 'is_correct' => false, 'answer_text' => 'Only Java', 'explanation' => 'Incorrect. Many languages support OOP.'],
            ['question_id' => 5, 'is_correct' => false, 'answer_text' => 'HTML and CSS', 'explanation' => 'Incorrect. These are markup and styling languages, not programming languages.'],

            // ==================== Question 6: Fourth OOP principle (fill_in_the_blank) ====================
            ['question_id' => 6, 'is_correct' => true, 'answer_text' => 'Polymorphism', 'blank_position' => 1, 'explanation' => 'Correct! The four pillars are Encapsulation, Abstraction, Inheritance, and Polymorphism.'],

            // ==================== Question 7: What is a class? (radio_button) ====================
            ['question_id' => 7, 'is_correct' => true, 'answer_text' => 'A blueprint for creating objects', 'explanation' => 'Correct! A class defines the structure and behavior for objects.'],
            ['question_id' => 7, 'is_correct' => false, 'answer_text' => 'An instance of an object', 'explanation' => 'Incorrect. An object is an instance of a class, not the other way around.'],
            ['question_id' => 7, 'is_correct' => false, 'answer_text' => 'A function', 'explanation' => 'Incorrect. A class can contain functions (methods).'],

            // ==================== Question 8: What is an object? (radio_button) ====================
            ['question_id' => 8, 'is_correct' => true, 'answer_text' => 'An instance of a class', 'explanation' => 'Correct! An object is created from a class template.'],
            ['question_id' => 8, 'is_correct' => false, 'answer_text' => 'A blueprint', 'explanation' => 'Incorrect. A class is the blueprint; an object is created from it.'],
            ['question_id' => 8, 'is_correct' => false, 'answer_text' => 'A method', 'explanation' => 'Incorrect. A method is a function inside a class.'],

            // ==================== Question 9: A class is a ___ for creating objects (fill_in_the_blank) ====================
            ['question_id' => 9, 'is_correct' => true, 'answer_text' => 'blueprint', 'blank_position' => 1, 'explanation' => 'Correct!'],
            ['question_id' => 9, 'is_correct' => true, 'answer_text' => 'template', 'blank_position' => 1, 'explanation' => 'Correct! Template is another valid term.'],

            // ==================== Question 10: Arrange steps (drag_and_drop) ====================
            ['question_id' => 10, 'is_correct' => true, 'answer_text' => 'Define class', 'drag_target' => '1', 'explanation' => 'Step 1: Define the class first.'],
            ['question_id' => 10, 'is_correct' => true, 'answer_text' => 'Instantiate object', 'drag_target' => '2', 'explanation' => 'Step 2: Create an object from the class.'],
            ['question_id' => 10, 'is_correct' => true, 'answer_text' => 'Use object methods', 'drag_target' => '3', 'explanation' => 'Step 3: Use the object.'],

            // ==================== Question 11: Keyword to create object in Java (radio_button) ====================
            ['question_id' => 11, 'is_correct' => true, 'answer_text' => 'new', 'explanation' => 'Correct! The new keyword creates a new object.'],
            ['question_id' => 11, 'is_correct' => false, 'answer_text' => 'create', 'explanation' => 'Incorrect. Java uses the new keyword.'],
            ['question_id' => 11, 'is_correct' => false, 'answer_text' => 'object', 'explanation' => 'Incorrect.'],

            // ==================== Question 12: Can class have multiple objects? (radio_button) ====================
            ['question_id' => 12, 'is_correct' => true, 'answer_text' => 'Yes', 'explanation' => 'Correct! A class can have multiple instances (objects).'],
            ['question_id' => 12, 'is_correct' => false, 'answer_text' => 'No', 'explanation' => 'Incorrect. You can create many objects from one class.'],

            // ==================== Question 13: What is a constructor? (radio_button) ====================
            ['question_id' => 13, 'is_correct' => true, 'answer_text' => 'A special method to initialize objects', 'explanation' => 'Correct! Constructors initialize new objects.'],
            ['question_id' => 13, 'is_correct' => false, 'answer_text' => 'A regular method', 'explanation' => 'Incorrect. Constructors are special methods.'],
            ['question_id' => 13, 'is_correct' => false, 'answer_text' => 'A variable', 'explanation' => 'Incorrect.'],

            // ==================== Question 14: Constructor called automatically (fill_in_the_blank) ====================
            ['question_id' => 14, 'is_correct' => true, 'answer_text' => 'constructor', 'blank_position' => 1, 'explanation' => 'Correct!'],

            // ==================== Question 15: What are attributes? (radio_button) ====================
            ['question_id' => 15, 'is_correct' => true, 'answer_text' => 'Variables that belong to a class', 'explanation' => 'Correct! Attributes are the data fields of a class.'],
            ['question_id' => 15, 'is_correct' => false, 'answer_text' => 'Methods of a class', 'explanation' => 'Incorrect. Methods are functions, not attributes.'],
            ['question_id' => 15, 'is_correct' => false, 'answer_text' => 'Classes', 'explanation' => 'Incorrect.'],

            // ==================== Question 16: Keyword for inheritance in Java (fill_in_the_blank) ====================
            ['question_id' => 16, 'is_correct' => true, 'answer_text' => 'extends', 'blank_position' => 1, 'explanation' => 'Correct! In Java, extends is used for inheritance.'],

            // ==================== Question 17: What is inheritance? (radio_button) ====================
            ['question_id' => 17, 'is_correct' => true, 'answer_text' => 'Acquiring properties from parent class', 'explanation' => 'Correct! Inheritance allows reuse of code.'],
            ['question_id' => 17, 'is_correct' => false, 'answer_text' => 'Creating new objects', 'explanation' => 'Incorrect.'],
            ['question_id' => 17, 'is_correct' => false, 'answer_text' => 'Hiding data', 'explanation' => 'Incorrect. That\'s encapsulation.'],

            // ==================== Question 18: Inheriting class is called ___ (fill_in_the_blank) ====================
            ['question_id' => 18, 'is_correct' => true, 'answer_text' => 'child', 'blank_position' => 1, 'explanation' => 'Correct!'],
            ['question_id' => 18, 'is_correct' => true, 'answer_text' => 'subclass', 'blank_position' => 1, 'explanation' => 'Correct!'],
            ['question_id' => 18, 'is_correct' => true, 'answer_text' => 'derived', 'blank_position' => 1, 'explanation' => 'Correct!'],

            // ==================== Question 19: Parent class also known as (radio_button) ====================
            ['question_id' => 19, 'is_correct' => true, 'answer_text' => 'Superclass or Base class', 'explanation' => 'Correct!'],
            ['question_id' => 19, 'is_correct' => false, 'answer_text' => 'Subclass', 'explanation' => 'Incorrect. That\'s the child class.'],
            ['question_id' => 19, 'is_correct' => false, 'answer_text' => 'Interface', 'explanation' => 'Incorrect.'],

            // ==================== Question 20: Subclass access private members? (radio_button) ====================
            ['question_id' => 20, 'is_correct' => true, 'answer_text' => 'No', 'explanation' => 'Correct! Private members are not accessible to subclasses.'],
            ['question_id' => 20, 'is_correct' => false, 'answer_text' => 'Yes', 'explanation' => 'Incorrect. Use protected for inherited access.'],

            // ==================== Question 21: Arrange inheritance hierarchy (drag_and_drop) ====================
            ['question_id' => 21, 'is_correct' => true, 'answer_text' => 'Base/Parent Class', 'drag_target' => '1', 'explanation' => 'Top of hierarchy.'],
            ['question_id' => 21, 'is_correct' => true, 'answer_text' => 'Intermediate Class', 'drag_target' => '2', 'explanation' => 'Middle.'],
            ['question_id' => 21, 'is_correct' => true, 'answer_text' => 'Derived/Child Class', 'drag_target' => '3', 'explanation' => 'Bottom.'],

            // ==================== Question 22: Java doesn't support (radio_button) ====================
            ['question_id' => 22, 'is_correct' => true, 'answer_text' => 'Multiple inheritance', 'explanation' => 'Correct! Java uses interfaces instead.'],
            ['question_id' => 22, 'is_correct' => false, 'answer_text' => 'Single inheritance', 'explanation' => 'Incorrect. Java supports this.'],
            ['question_id' => 22, 'is_correct' => false, 'answer_text' => 'Multilevel inheritance', 'explanation' => 'Incorrect. Java supports this.'],

            // ==================== Question 23: Call parent constructor (fill_in_the_blank) ====================
            ['question_id' => 23, 'is_correct' => true, 'answer_text' => 'super', 'blank_position' => 1, 'explanation' => 'Correct! super() calls the parent constructor.'],

            // ==================== Question 24: Compile time polymorphism (radio_button) ====================
            ['question_id' => 24, 'is_correct' => true, 'answer_text' => 'Method Overloading', 'explanation' => 'Correct! Method overloading is compile-time polymorphism.'],
            ['question_id' => 24, 'is_correct' => false, 'answer_text' => 'Method Overriding', 'explanation' => 'Incorrect. Method overriding is runtime polymorphism.'],
            ['question_id' => 24, 'is_correct' => false, 'answer_text' => 'Encapsulation', 'explanation' => 'Incorrect.'],

            // ==================== Question 25: What is polymorphism? (radio_button) ====================
            ['question_id' => 25, 'is_correct' => true, 'answer_text' => 'Many forms of a single entity', 'explanation' => 'Correct! Poly = many, morph = forms.'],
            ['question_id' => 25, 'is_correct' => false, 'answer_text' => 'Hiding implementation', 'explanation' => 'Incorrect. That\'s abstraction.'],
            ['question_id' => 25, 'is_correct' => false, 'answer_text' => 'Code reuse', 'explanation' => 'Incorrect. That\'s primarily inheritance.'],

            // ==================== Question 26: Method ___ allows same name different params (fill_in_the_blank) ====================
            ['question_id' => 26, 'is_correct' => true, 'answer_text' => 'overloading', 'blank_position' => 1, 'explanation' => 'Correct!'],

            // ==================== Question 27: What is method overriding? (radio_button) ====================
            ['question_id' => 27, 'is_correct' => true, 'answer_text' => 'Redefining parent method in child class', 'explanation' => 'Correct!'],
            ['question_id' => 27, 'is_correct' => false, 'answer_text' => 'Same method name different parameters', 'explanation' => 'Incorrect. That\'s overloading.'],
            ['question_id' => 27, 'is_correct' => false, 'answer_text' => 'Creating new method', 'explanation' => 'Incorrect.'],

            // ==================== Question 28: Annotation for overriding (radio_button) ====================
            ['question_id' => 28, 'is_correct' => true, 'answer_text' => '@Override', 'explanation' => 'Correct!'],
            ['question_id' => 28, 'is_correct' => false, 'answer_text' => '@Overload', 'explanation' => 'Incorrect.'],
            ['question_id' => 28, 'is_correct' => false, 'answer_text' => '@Inherit', 'explanation' => 'Incorrect.'],

            // ==================== Question 29: Polymorphism common ___ class (fill_in_the_blank) ====================
            ['question_id' => 29, 'is_correct' => true, 'answer_text' => 'parent', 'blank_position' => 1, 'explanation' => 'Correct!'],
            ['question_id' => 29, 'is_correct' => true, 'answer_text' => 'super', 'blank_position' => 1, 'explanation' => 'Correct!'],
            ['question_id' => 29, 'is_correct' => true, 'answer_text' => 'base', 'blank_position' => 1, 'explanation' => 'Correct!'],

            // ==================== Question 30: Arrange polymorphism by time (drag_and_drop) ====================
            ['question_id' => 30, 'is_correct' => true, 'answer_text' => 'Compile-time (Overloading)', 'drag_target' => '1', 'explanation' => 'Happens first.'],
            ['question_id' => 30, 'is_correct' => true, 'answer_text' => 'Runtime (Overriding)', 'drag_target' => '2', 'explanation' => 'Happens during execution.'],

            // ==================== Question 31: Main benefit of encapsulation (radio_button) ====================
            ['question_id' => 31, 'is_correct' => true, 'answer_text' => 'Data hiding and security', 'explanation' => 'Correct! Encapsulation provides data security.'],
            ['question_id' => 31, 'is_correct' => false, 'answer_text' => 'Faster execution', 'explanation' => 'Incorrect. Encapsulation is about data protection, not speed.'],
            ['question_id' => 31, 'is_correct' => false, 'answer_text' => 'Code reuse', 'explanation' => 'Incorrect. That\'s inheritance.'],

            // ==================== Question 32: What is encapsulation? (radio_button) ====================
            ['question_id' => 32, 'is_correct' => true, 'answer_text' => 'Bundling data and methods together', 'explanation' => 'Correct! It wraps data and the code that manipulates it.'],
            ['question_id' => 32, 'is_correct' => false, 'answer_text' => 'Inheriting properties', 'explanation' => 'Incorrect. That\'s inheritance.'],
            ['question_id' => 32, 'is_correct' => false, 'answer_text' => 'Method overloading', 'explanation' => 'Incorrect. That\'s polymorphism.'],

            // ==================== Question 33: private access modifier (fill_in_the_blank) ====================
            ['question_id' => 33, 'is_correct' => true, 'answer_text' => 'private', 'blank_position' => 1, 'explanation' => 'Correct!'],

            // ==================== Question 34: Getter and setter methods (radio_button) ====================
            ['question_id' => 34, 'is_correct' => true, 'answer_text' => 'Accessing and modifying private data', 'explanation' => 'Correct! They provide controlled access to private fields.'],
            ['question_id' => 34, 'is_correct' => false, 'answer_text' => 'Creating objects', 'explanation' => 'Incorrect. That\'s a constructor\'s job.'],
            ['question_id' => 34, 'is_correct' => false, 'answer_text' => 'Method overriding', 'explanation' => 'Incorrect.'],

            // ==================== Question 35: Most restrictive modifier (radio_button) ====================
            ['question_id' => 35, 'is_correct' => true, 'answer_text' => 'private', 'explanation' => 'Correct! Private is the most restrictive.'],
            ['question_id' => 35, 'is_correct' => false, 'answer_text' => 'public', 'explanation' => 'Incorrect. Public is the least restrictive.'],
            ['question_id' => 35, 'is_correct' => false, 'answer_text' => 'protected', 'explanation' => 'Incorrect. Protected is less restrictive than private.'],

            // ==================== Question 36: Arrange access modifiers (drag_and_drop) ====================
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'private', 'drag_target' => '1', 'explanation' => 'Most restrictive.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'default', 'drag_target' => '2', 'explanation' => 'Package-private.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'protected', 'drag_target' => '3', 'explanation' => 'Accessible to subclasses.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'public', 'drag_target' => '4', 'explanation' => 'Least restrictive.'],

            // ==================== Question 37: Encapsulation is data ___ (fill_in_the_blank) ====================
            ['question_id' => 37, 'is_correct' => true, 'answer_text' => 'hiding', 'blank_position' => 1, 'explanation' => 'Correct! Encapsulation hides internal details.'],
        ];

        foreach ($answers as $answer) {
            Answer::create($answer);
        }
    }
}
