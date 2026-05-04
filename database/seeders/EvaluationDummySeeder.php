<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MslqAnswer;
use App\Models\MslqQuestion;
use App\Models\MslqResult;
use App\Models\Role;
use App\Models\SusAnswer;
use App\Models\SusQuestion;
use App\Models\SusResult;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * EvaluationDummySeeder
 * Generates 40 dummy students across 2 classes with MSLQ and SUS results.
 */
final class EvaluationDummySeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('role_name', 'mahasiswa')->first();
        if (! $role) {
            $this->command->error('Role mahasiswa not found. Run RoleSeeder first.');

            return;
        }

        $mslqQuestions = MslqQuestion::all();
        $susQuestions  = SusQuestion::all();

        if ($mslqQuestions->isEmpty() || $susQuestions->isEmpty()) {
            $this->command->error('Questions not found. Run Question seeders first.');

            return;
        }

        $types = ['pre', 'post'];

        $this->command->info('Generating 40 dummy students with analysis data...');

        foreach ($types as $type) {
            // Adjust means for T-Test variety
            // pre: Mid motivation (3-5), Mid Strategy (3-5)
            // post: High motivation (5-7), High Strategy (5-7)
            $motivationRange = $type === 'pre' ? [3, 5] : [5, 7];
            $strategyRange   = $type === 'pre' ? [3, 5] : [4, 7];
            $susRange        = $type === 'pre' ? [2, 4] : [4, 5]; // Post-test liked it more

            for ($i = 1; $i <= 20; $i++) {
                $name = 'Student ' . Str::random(5) . " ($type)";
                $user = User::create([
                    'name'        => $name,
                    'email'       => strtolower(Str::slug($name)) . '@example.com',
                    'password'    => Hash::make('password'),
                    'role_id'     => $role->id,
                    'is_approved' => true,
                ]);

                // 1. Generate MSLQ
                $this->seedMslq($user, $type, $mslqQuestions, $motivationRange, $strategyRange);

                // 2. Generate SUS
                $this->seedSus($user, $type, $susQuestions, $susRange);
            }
        }

        $this->command->info('Successfully seeded 40 students with evaluation data.');
    }

    private function seedMslq($user, $type, $questions, $mRange, $sRange): void
    {
        $result = MslqResult::create([
            'user_id'          => $user->id,
            'assessment_type'  => $type,
            'scores_by_scale'  => [],
            'total_motivation' => 0,
            'total_strategy'   => 0,
        ]);

        $scaleTotals = [];
        $scaleCounts = [];
        $catTotals   = ['motivation' => 0, 'learning_strategy' => 0];
        $catCounts   = ['motivation' => 0, 'learning_strategy' => 0];

        foreach ($questions as $q) {
            $range = $q->category->value === 'motivation' ? $mRange : $sRange;
            $value = rand($range[0], $range[1]);

            MslqAnswer::create([
                'mslq_result_id'   => $result->id,
                'mslq_question_id' => $q->id,
                'value'            => $value,
            ]);

            // Calculate contribution
            $actualValue = $q->is_reverse ? (8 - $value) : $value;

            $scaleKey = $q->scale->value;
            $catKey   = $q->category->value;

            $scaleTotals[$scaleKey] = ($scaleTotals[$scaleKey] ?? 0) + $actualValue;
            $scaleCounts[$scaleKey] = ($scaleCounts[$scaleKey] ?? 0) + 1;

            $catTotals[$catKey] += $actualValue;
            $catCounts[$catKey]++;
        }

        $scoresByScale = [];
        foreach ($scaleTotals as $scale => $total) {
            $scoresByScale[$scale] = round($total / $scaleCounts[$scale], 2);
        }

        $result->update([
            'scores_by_scale'  => $scoresByScale,
            'total_motivation' => round($catTotals['motivation'] / $catCounts['motivation'], 2),
            'total_strategy'   => round($catTotals['learning_strategy'] / $catCounts['learning_strategy'], 2),
        ]);
    }

    private function seedSus($user, $type, $questions, $range): void
    {
        $susData = [
            'user_id'         => $user->id,
            'assessment_type' => $type,
            'comments'        => 'Dummy comment for ' . $user->name,
            'total_score'     => 0,
        ];

        $totalContribution = 0;
        $answers           = [];
        foreach ($questions as $index => $q) {
            $value          = rand($range[0], $range[1]);
            $qKey           = 'q' . ($index + 1);
            $susData[$qKey] = $value;

            $answers[] = [
                'id'              => Str::ulid()->toBase32(),
                'sus_question_id' => $q->id,
                'value'           => $value,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            if ($q->is_reverse) {
                $totalContribution += (5 - $value);
            } else {
                $totalContribution += ($value - 1);
            }
        }

        $susData['total_score'] = $totalContribution * 2.5;
        $result                 = SusResult::create($susData);

        foreach ($answers as $answer) {
            $answer['sus_result_id'] = $result->id;
            SusAnswer::create($answer);
        }
    }
}
