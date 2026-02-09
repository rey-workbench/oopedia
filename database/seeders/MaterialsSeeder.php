<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'title' => 'Introduction to Object-Oriented Programming',
                'content' => 'Object-oriented programming (OOP) is a programming paradigm based on the concept of "objects", which can contain data and code: data in the form of fields (often known as attributes or properties), and code, in the form of procedures (often known as methods).',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Classes and Objects',
                'content' => 'A class is a blueprint for creating objects. An object is an instance of a class. Classes define the properties and behaviors that objects of that class will have.',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Inheritance',
                'content' => 'Inheritance is a mechanism in which one class acquires the property of another class. For example, a child class inherits the properties and methods of its parent class.',
                'module_id' => 3, // Inheritance
                'created_by' => 2,
            ],
            [
                'title' => 'Polymorphism',
                'content' => 'Polymorphism allows objects of different classes to be treated as objects of a common super class. It is the ability of an object to take on many forms.',
                'module_id' => 4, // Polymorphism
                'created_by' => 2,
            ],
            [
                'title' => 'Encapsulation',
                'content' => 'Encapsulation is the bundling of data and methods that operate on that data within a single unit, or object, and restricting access to some of the object\'s components.',
                'module_id' => 2, // Encapsulation
                'created_by' => 2,
            ],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(
                ['title' => $material['title']],
                $material
            );
        }
    }
}
