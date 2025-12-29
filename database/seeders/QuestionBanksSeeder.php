<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionBank;

class QuestionBanksSeeder extends Seeder
{
    public function run(): void
    {
        $questionBanks = [
            [
                'name' => 'OOP Fundamentals Quiz',
                'description' => 'Basic questions about Object-Oriented Programming concepts',
                'material_id' => 1,
                'created_by' => 2, // Admin
            ],
            [
                'name' => 'Advanced OOP Topics',
                'description' => 'Questions covering inheritance, polymorphism, and encapsulation',
                'material_id' => 3,
                'created_by' => 2,
            ],
            [
                'name' => 'Class and Object Practice',
                'description' => 'Practice questions for understanding classes and objects',
                'material_id' => 2,
                'created_by' => 2,
            ],
        ];

        foreach ($questionBanks as $bank) {
            QuestionBank::create($bank);
        }
    }
}
