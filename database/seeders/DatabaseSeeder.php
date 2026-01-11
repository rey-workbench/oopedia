<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperadminSeeder::class,
            AdminSeeder::class,
            MahasiswaSeeder::class,
            MaterialsSeeder::class,
            SubMaterialsSeeder::class, // NEW
            QuestionsSeeder::class,
            AnswersSeeder::class,
            UeqSurveysSeeder::class,
        ]);

    }
}
