<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class ProgressHelper
{
    /**
     * Calculate the total number of configured questions across given materials.
     *
     * @param  Collection  $materials  Collection of materials (needs to have 'questions' relation loaded)
     * @return int
     */
    public static function calculateTotalQuestions(Collection $materials): int
    {
        return $materials->sum(fn($m) => $m->questions->count());
    }

    /**
     * Calculate progress percentage based on answered questions and total questions.
     *
     * @param  int  $answeredQuestions
     * @param  int  $totalQuestions
     * @return int Progress percentage (0-100)
     */
    public static function calculateProgressPercentage(int $answeredQuestions, int $totalQuestions): int
    {
        return $totalQuestions > 0
            ? min(100, (int)round(($answeredQuestions / $totalQuestions) * 100))
            : 0;
    }

    /**
     * Calculate total questions grouped by difficulty.
     *
     * @param  Collection  $materials
     * @return array<string, int> Formatted as ['beginner' => 0, 'medium' => 0, 'hard' => 0]
     */
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
     * Calculate question counts for a single material, grouped by difficulty.
     * Returns configured counts (guest caps applied) using keys 'easy','medium','hard','total'.
     *
     * @param  mixed  $material  Material model instance with loaded questions relation
     * @param  bool   $isGuest   Whether to apply guest limits per difficulty
     * @return array<string,int>
     */
    public static function calculateMaterialQuestionCounts($material, bool $isGuest = false): array
    {
        $guestLimit = 3; // guests see up to N configured questions per difficulty

        $beginner = $material->questions->where('difficulty', 'beginner')->count();
        $medium   = $material->questions->where('difficulty', 'medium')->count();
        $hard     = $material->questions->where('difficulty', 'hard')->count();

        $configuredBeginner = $isGuest ? min($beginner, $guestLimit) : $beginner;
        $configuredMedium   = $isGuest ? min($medium, $guestLimit)   : $medium;
        $configuredHard     = $isGuest ? min($hard, $guestLimit)     : $hard;

        $total = $configuredBeginner + $configuredMedium + $configuredHard;

        return [
            'easy'   => $configuredBeginner,
            'medium' => $configuredMedium,
            'hard'   => $configuredHard,
            'total'  => $total,
        ];
    }
}
