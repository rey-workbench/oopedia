<?php

declare(strict_types=1);

namespace App\DTOs\Quiz;

/**
 * Data Transfer Object for material progress query parameters.
 */
final readonly class MaterialProgressDTO
{
    public function __construct(
        public string $userId,
        public bool $isGuest,
        public array $guestProgress = [],
    ) {
    }
}
