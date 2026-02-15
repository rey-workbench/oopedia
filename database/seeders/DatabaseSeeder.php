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
            DosenSeeder::class,
            MahasiswaSeeder::class,
            MaterialsSeeder::class,
            SubMaterialsSeeder::class,
            ComprehensiveQuestionsSeeder::class, // NEW: Comprehensive Q&A seeder
            // QuestionsSeeder::class, // OLD: Replaced by comprehensive version
            // AnswersSeeder::class,   // OLD: Now integrated in comprehensive seeder
            UeqSurveysSeeder::class,
        ]);

    }
}
