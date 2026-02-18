<?php

namespace App\Traits;

use App\Models\Material;

/**
 * Provides a helper for computing the configured question count for a material.
 *
 * For guests  : max 3 questions per difficulty level (beginner / medium / hard).
 * For students: all available questions.
 *
 * Prerequisites: the $material must have the `questions` relation eager-loaded.
 */
trait CalculatesConfiguredQuestions
{
    protected function calculateConfiguredQuestions(Material $material, bool $isGuest): int
    {
        if ($isGuest) {
            $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
            $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
            $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());

            return $beginnerCount + $mediumCount + $hardCount;
        }

        return $material->questions->count();
    }
}
