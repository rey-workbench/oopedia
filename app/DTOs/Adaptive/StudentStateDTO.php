<?php

declare(strict_types=1);

namespace App\DTOs\Adaptive;

/**
 * Data Transfer Object for Student Learning State.
 * Encapsulates the complex nested array structure of student performance.
 */
class StudentStateDTO
{
    public function __construct(
        private readonly array $state,
    ) {
    }

    public static function fromArray(array $state): self
    {
        return new self($state);
    }

    public function getMetric(string $key, mixed $default = null): mixed
    {
        return data_get($this->state, $key, $default);
    }
}
