<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Progress;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $progress = [
            // Mahasiswa 1 progress
            [
                'user_id' => 4, // Assuming mahasiswa user id
                'material_id' => 1,
                'question_id' => 1,
                'is_answered' => true,
                'is_correct' => true,
                'attempt_number' => 1,
            ],
            [
                'user_id' => 4,
                'material_id' => 1,
                'question_id' => 2,
                'is_answered' => true,
                'is_correct' => false,
                'attempt_number' => 1,
            ],
            [
                'user_id' => 4,
                'material_id' => 2,
                'question_id' => 3,
                'is_answered' => true,
                'is_correct' => true,
                'attempt_number' => 1,
            ],
            // Mahasiswa 2 progress
            [
                'user_id' => 5, // Another mahasiswa
                'material_id' => 1,
                'question_id' => 1,
                'is_answered' => true,
                'is_correct' => true,
                'attempt_number' => 1,
            ],
            [
                'user_id' => 5,
                'material_id' => 1,
                'question_id' => 2,
                'is_answered' => true,
                'is_correct' => true,
                'attempt_number' => 2, // Second attempt
            ],
        ];

        foreach ($progress as $prog) {
            Progress::create($prog);
        }
    }
}
