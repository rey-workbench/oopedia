<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\SusAnswer;
use App\Models\SusQuestion;
use App\Models\SusResult;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * SusResultsSeeder.
 * Generates dummy SUS results for evaluation testing.
 */
final class SusResultsSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = User::whereHas('role', function ($q) {
            $q->where('role_name', RoleName::MAHASISWA);
        })->take(2)->get();

        if ($mahasiswas->isEmpty()) {
            return;
        }

        $questions = SusQuestion::orderBy('order')->get();
        if ($questions->count() < 10) {
            $this->command->error('SusQuestions not found. Please run SusQuestionSeeder first.');

            return;
        }

        $dummyAnswers = [
            [5, 2, 5, 1, 5, 2, 5, 1, 5, 1], // Student 1 answers
            [4, 2, 4, 2, 4, 3, 4, 2, 4, 2], // Student 2 answers
        ];

        foreach ($mahasiswas as $index => $user) {
            $answers = $dummyAnswers[$index] ?? [4, 2, 4, 2, 4, 2, 4, 2, 4, 2];

            $result = SusResult::updateOrCreate(
                ['user_id' => $user->id, 'assessment_type' => 'pre'],
                [
                    'comments'    => 'Sistem sangat membantu belajar OOP.',
                    'suggestions' => 'Perbanyak variasi soal.',
                    'total_score' => 0, // Will update after answers
                ],
            );

            $totalContribution = 0;

            foreach ($questions as $qIndex => $question) {
                $value = $answers[$qIndex] ?? 3;

                SusAnswer::updateOrCreate(
                    [
                        'sus_result_id'   => $result->id,
                        'sus_question_id' => $question->id,
                    ],
                    ['value' => $value],
                );

                // SUS Scoring Logic
                if ($question->is_reverse) {
                    $totalContribution += (5 - $value);
                } else {
                    $totalContribution += ($value - 1);
                }
            }

            $result->update(['total_score' => $totalContribution * 2.5]);
        }
    }
}
