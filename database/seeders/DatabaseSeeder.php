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
            RealQuestionSeeder::class,
            // ComprehensiveQuestionsSeeder::class,
            FinalProjectSeeder::class,
            MslqOopediaSeeder::class,
            SusQuestionSeeder::class,
            // MslqQuestionSeeder::class,
            AdaptiveRuleSeeder::class,
        ]);
    }
}
