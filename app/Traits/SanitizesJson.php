<?php

declare(strict_types=1);

namespace App\Traits;

trait SanitizesJson
{
    /**
     * Recursively sanitize array values to prevent JSON encoding errors with NaN/INF.
     */
    protected function sanitizeForJson(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeForJson'], $value);
        }

        if (is_float($value) && (is_nan($value) || is_infinite($value))) {
            return null;
        }

        return $value;
    }
}
