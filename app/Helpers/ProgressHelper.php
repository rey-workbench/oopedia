<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class ProgressHelper
{
    public static function calculateTotalQuestions(Collection $materials): int
    {
        return $materials->sum(fn ($m) => $m->questions->count());
    }

    public static function calculateProgressPercentage(int $answeredQuestions, int $totalQuestions): int
    {
        return $totalQuestions > 0
            ? min(100, (int) round(($answeredQuestions / $totalQuestions) * 100))
            : 0;
    }

    public static function calculateDifficultyTotals(Collection $materials): array
    {
        $totals = ['beginner' => 0, 'medium' => 0, 'hard' => 0];

        foreach ($materials as $material) {
            foreach ($material->questions as $question) {
                if (isset($totals[$question->difficulty])) {
                    $totals[$question->difficulty]++;
                }
            }
        }

        return $totals;
    }

    /**
     * @param mixed $material Material model instance with loaded questions relation
     * @param bool $isGuest Whether to apply guest limits per difficulty
     * @return array<string,int>
     */
    public static function calculateMaterialQuestionCounts($material, bool $isGuest = false): array
    {
        $guestLimit = 3;

        $beginner = $material->questions->where('difficulty', 'beginner')->count();
        $medium   = $material->questions->where('difficulty', 'medium')->count();
        $hard     = $material->questions->where('difficulty', 'hard')->count();

        $configuredBeginner = $isGuest ? min($beginner, $guestLimit) : $beginner;
        $configuredMedium   = $isGuest ? min($medium, $guestLimit) : $medium;
        $configuredHard     = $isGuest ? min($hard, $guestLimit) : $hard;

        $total = $configuredBeginner + $configuredMedium + $configuredHard;

        return [
            'easy'   => $configuredBeginner,
            'medium' => $configuredMedium,
            'hard'   => $configuredHard,
            'total'  => $total,
        ];
    }
}
