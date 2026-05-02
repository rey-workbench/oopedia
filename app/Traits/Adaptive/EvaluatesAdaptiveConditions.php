<?php

declare(strict_types=1);

namespace App\Traits\Adaptive;

/**
 * Trait untuk membantu evaluasi operator matematis dinamis pada Adaptive Engine.
 */
trait EvaluatesAdaptiveConditions
{
    /**
     * Mengevaluasi apakah suatu nilai memenuhi kondisi operator terhadap threshold.
     */
    protected function evaluateCondition(mixed $value, string $operator, mixed $threshold): bool
    {
        return match ($operator) {
            '<'       => $value < $threshold,
            '>'       => $value > $threshold,
            '<='      => $value <= $threshold,
            '>='      => $value >= $threshold,
            '=='      => $value == $threshold,
            '!='      => $value != $threshold,
            'in'      => in_array($value, (array) $threshold),
            'between' => is_array($threshold) && count($threshold) >= 2 && ($value >= $threshold[0] && $value <= $threshold[1]),
            default   => false,
        };
    }
}
