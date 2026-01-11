<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubMaterial;

class SubMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $subMaterials = [
            // ==================== Material 1: Introduction to OOP ====================
            [
                'material_id' => 1,
                'title' => 'What is OOP?',
                'content' => 'OOP is a programming paradigm that organizes code around objects and data rather than actions and logic.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 1,
                'title' => 'OOP Principles',
                'content' => 'The four main principles of OOP are: Encapsulation, Abstraction, Inheritance, and Polymorphism.',
                'jenis_konten' => 'teori',
                'order' => 2,
            ],
            
            // ==================== Material 2: Classes and Objects ====================
            [
                'material_id' => 2,
                'title' => 'Understanding Classes',
                'content' => 'A class is a blueprint or template for creating objects. It defines the structure and behavior.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 2,
                'title' => 'Creating Objects',
                'content' => 'Objects are instances of classes. You create them using the "new" keyword in most OOP languages.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 2,
                'title' => 'Constructors and Methods',
                'content' => 'Constructors initialize objects, while methods define object behaviors.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],
            
            // ==================== Material 3: Inheritance ====================
            [
                'material_id' => 3,
                'title' => 'Inheritance Basics',
                'content' => 'Inheritance allows a class to inherit properties and methods from another class.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 3,
                'title' => 'Implementing Inheritance',
                'content' => 'Use the "extends" keyword in Java to create a subclass that inherits from a parent class.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 3,
                'title' => 'Types of Inheritance',
                'content' => 'Single, multilevel, hierarchical inheritance. Java doesn\'t support multiple inheritance directly.',
                'jenis_konten' => 'teori',
                'order' => 3,
            ],
            
            // ==================== Material 4: Polymorphism ====================
            [
                'material_id' => 4,
                'title' => 'Polymorphism Concept',
                'content' => 'Polymorphism means "many forms" - objects can take multiple forms depending on context.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 4,
                'title' => 'Method Overloading',
                'content' => 'Compile-time polymorphism: multiple methods with same name but different parameters.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 4,
                'title' => 'Method Overriding',
                'content' => 'Runtime polymorphism: subclass provides specific implementation of parent class method.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],
            
            // ==================== Material 5: Encapsulation ====================
            [
                'material_id' => 5,
                'title' => 'Encapsulation Principles',
                'content' => 'Encapsulation bundles data and methods together, hiding internal details from outside.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 5,
                'title' => 'Access Modifiers',
                'content' => 'Private, protected, public, and default access modifiers control visibility of class members.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 5,
                'title' => 'Getters and Setters',
                'content' => 'Getter and setter methods provide controlled access to private variables.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],
        ];

        foreach ($subMaterials as $subMaterial) {
            SubMaterial::create($subMaterial);
        }
    }
}
