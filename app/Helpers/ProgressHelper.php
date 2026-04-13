<?php

namespace App\Helpers;

use App\Enums\Lms\QuestionDifficulty;
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
                $difficulty = $question->difficulty instanceof QuestionDifficulty ? $question->difficulty->value : $question->difficulty;
                if (isset($totals[$difficulty])) {
                    $totals[$difficulty]++;
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

        $beginner = $material->questions->filter(fn ($q) => ($q->difficulty instanceof QuestionDifficulty ? $q->difficulty->value : $q->difficulty) === 'beginner')->count();
        $medium   = $material->questions->filter(fn ($q) => ($q->difficulty instanceof QuestionDifficulty ? $q->difficulty->value : $q->difficulty) === 'medium')->count();
        $hard     = $material->questions->filter(fn ($q) => ($q->difficulty instanceof QuestionDifficulty ? $q->difficulty->value : $q->difficulty) === 'hard')->count();

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
